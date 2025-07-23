{{-- 
==============================================
COMPLETE CREATE ROLE FORM
File: resources/views/roles/create.blade.php
==============================================
--}}
@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; color: #333;">🔑 Create New Role</h2>
            <p style="color: #666; margin: 5px 0 0 0;">Define a new role with specific permissions</p>
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
                    <h4 style="margin-bottom: 20px; color: #333;">📋 Role Information</h4>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Role Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="e.g., content_manager, sales_lead"
                               style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px;">
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Use lowercase with underscores (will be displayed as "Content Manager")</div>
                        @error('name')
                            <div style="color: #f44336; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Permissions -->
                <div class="content-card">
                    <h4 style="margin-bottom: 20px; color: #333;">🔐 Permissions</h4>
                    
                    @php
                        $groupedPermissions = collect($allPermissions)->groupBy('category');
                    @endphp
                    
                    @foreach($groupedPermissions as $category => $permissions)
                    <div style="margin-bottom: 25px; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
                        <h6 style="color: #333; margin-bottom: 15px; text-transform: capitalize; display: flex; align-items: center; gap: 8px;">
                            @switch($category)
                                @case('admin')
                                    <span style="color: #f44336;">⚡</span> Admin
                                    @break
                                @case('general')
                                    <span style="color: #2196f3;">👤</span> General
                                    @break
                                @case('assets')
                                    <span style="color: #4caf50;">📦</span> Assets
                                    @break
                                @case('clients')
                                    <span style="color: #ff9800;">🏢</span> Clients
                                    @break
                                @case('management')
                                    <span style="color: #9c27b0;">👥</span> Management
                                    @break
                                @case('technical')
                                    <span style="color: #00bcd4;">🔧</span> Technical
                                    @break
                                @default
                                    <span>📋</span> {{ ucfirst($category) }}
                            @endswitch
                        </h6>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px;">
                            @foreach($permissions as $key => $permission)
                            <div style="border: 1px solid #ddd; border-radius: 6px; padding: 12px; transition: all 0.2s ease;" 
                                 onclick="togglePermission('{{ $key }}')" 
                                 id="permission-{{ $key }}"
                                 data-permission="{{ $key }}">
                                <label style="display: flex; align-items: start; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                           {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}
                                           onchange="updatePermissionCard(this)">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 500; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
                                            {{ $permission['name'] }}
                                            @if(isset($permission['danger']) && $permission['danger'])
                                                <span style="background: #ffebee; color: #d32f2f; padding: 2px 6px; border-radius: 8px; font-size: 10px;">
                                                    DANGER
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size: 12px; color: #666;">{{ $permission['description'] }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Selected Permissions Preview -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">📋 Selected Permissions</h4>
                    
                    <div id="selected-permissions" style="min-height: 100px;">
                        <div style="color: #666; font-style: italic; text-align: center; padding: 20px;">
                            No permissions selected
                        </div>
                    </div>
                </div>

                <!-- Role Templates -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">🎨 Quick Templates</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <button type="button" onclick="applyTemplate('basic_employee')" class="template-btn">
                            👤 Basic Employee
                        </button>
                        <button type="button" onclick="applyTemplate('team_lead')" class="template-btn">
                            👥 Team Lead
                        </button>
                        <button type="button" onclick="applyTemplate('department_manager')" class="template-btn">
                            🏢 Department Manager
                        </button>
                        <button type="button" onclick="applyTemplate('technical_staff')" class="template-btn">
                            🔧 Technical Staff
                        </button>
                        <button type="button" onclick="clearAll()" class="template-btn" style="background: #ffebee; color: #d32f2f;">
                            🗑️ Clear All
                        </button>
                    </div>
                </div>

                <!-- Permission Summary -->
                <div class="content-card" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #333;">📊 Summary</h4>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Total Permissions:</span>
                        <span id="total-count" style="font-weight: bold;">0</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Admin Level:</span>
                        <span id="admin-level" style="font-weight: bold; color: #4caf50;">Safe</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between;">
                        <span>Categories:</span>
                        <span id="category-count" style="font-weight: bold;">0</span>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="content-card">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                            🎉 Create Role
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn" style="width: 100%; text-align: center;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.content-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn {
    padding: 8px 16px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background: white;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn:hover {
    border-color: #2196f3;
    color: #2196f3;
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

.template-btn {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f8f9fa;
    color: #333;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
}

.template-btn:hover {
    background: #e9ecef;
    border-color: #2196f3;
}

.permission-card-selected {
    border-color: #2196f3 !important;
    background: #e3f2fd !important;
}

.permission-card-danger {
    border-color: #f44336 !important;
    background: #ffebee !important;
}
</style>

<script>
// Permission templates
const templates = {
    'basic_employee': ['view_own_data', 'request_assets', 'view_clients'],
    'team_lead': ['view_own_data', 'request_assets', 'view_clients', 'approve_requests', 'view_reports'],
    'department_manager': ['view_dashboard', 'manage_team', 'view_clients', 'view_reports', 'approve_requests', 'view_own_data'],
    'technical_staff': ['view_jobs', 'view_terminals', 'update_terminals', 'view_own_data']
};

function applyTemplate(templateName) {
    if (!templates[templateName]) return;
    
    // Clear all first
    clearAll();
    
    // Apply template permissions
    templates[templateName].forEach(permission => {
        const checkbox = document.querySelector(`input[value="${permission}"]`);
        if (checkbox) {
            checkbox.checked = true;
            updatePermissionCard(checkbox);
        }
    });
    
    updateSelectedPermissions();
    updateSummary();
}

function clearAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
        updatePermissionCard(checkbox);
    });
    
    updateSelectedPermissions();
    updateSummary();
}

function togglePermission(permissionKey) {
    const checkbox = document.querySelector(`input[value="${permissionKey}"]`);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        updatePermissionCard(checkbox);
        updateSelectedPermissions();
        updateSummary();
    }
}

function updatePermissionCard(checkbox) {
    const card = checkbox.closest('div[id^="permission-"]');
    if (checkbox.checked) {
        card.classList.add('permission-card-selected');
        if (checkbox.value === 'all') {
            card.classList.add('permission-card-danger');
        }
    } else {
        card.classList.remove('permission-card-selected', 'permission-card-danger');
    }
}

function updateSelectedPermissions() {
    const selectedDiv = document.getElementById('selected-permissions');
    const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');
    
    if (checkedBoxes.length === 0) {
        selectedDiv.innerHTML = '<div style="color: #666; font-style: italic; text-align: center; padding: 20px;">No permissions selected</div>';
        return;
    }
    
    let html = '';
    checkedBoxes.forEach(checkbox => {
        const permission = checkbox.value;
        const card = checkbox.closest('div[id^="permission-"]');
        const nameElement = card.querySelector('div[style*="font-weight: 500"]');
        const name = nameElement ? nameElement.textContent.trim().split('\n')[0].trim() : permission;
        
        const colors = {
            'all': {bg: '#ffebee', text: '#d32f2f'},
            'view_dashboard': {bg: '#e3f2fd', text: '#1976d2'},
            'manage_assets': {bg: '#e8f5e8', text: '#388e3c'},
            'view_clients': {bg: '#fff3e0', text: '#f57c00'},
            'manage_team': {bg: '#f3e5f5', text: '#7b1fa2'},
            'view_jobs': {bg: '#e0f2f1', text: '#00796b'}
        };
        
        const color = colors[permission] || {bg: '#f5f5f5', text: '#666'};
        
        html += `<span style="background: ${color.bg}; color: ${color.text}; padding: 4px 8px; border-radius: 8px; font-size: 11px; margin: 2px; display: inline-block;">${name}</span>`;
    });
    
    selectedDiv.innerHTML = html;
}

function updateSummary() {
    const checkedBoxes = document.querySelectorAll('input[name="permissions[]"]:checked');
    const totalCount = checkedBoxes.length;
    
    // Update total count
    document.getElementById('total-count').textContent = totalCount;
    
    // Update admin level
    const adminLevel = document.getElementById('admin-level');
    const hasAll = Array.from(checkedBoxes).some(cb => cb.value === 'all');
    const hasManageTeam = Array.from(checkedBoxes).some(cb => cb.value === 'manage_team');
    const hasManageAssets = Array.from(checkedBoxes).some(cb => cb.value === 'manage_assets');
    
    if (hasAll) {
        adminLevel.textContent = 'Super Admin';
        adminLevel.style.color = '#f44336';
    } else if (hasManageTeam || hasManageAssets) {
        adminLevel.textContent = 'Admin';
        adminLevel.style.color = '#ff9800';
    } else if (totalCount > 3) {
        adminLevel.textContent = 'Elevated';
        adminLevel.style.color = '#2196f3';
    } else {
        adminLevel.textContent = 'Safe';
        adminLevel.style.color = '#4caf50';
    }
    
    // Update category count
    const categories = new Set();
    checkedBoxes.forEach(checkbox => {
        const permissionCard = checkbox.closest('div[id^="permission-"]');
        const categorySection = permissionCard.closest('div[style*="border: 1px solid #e0e0e0"]');
        if (categorySection) {
            const categoryHeader = categorySection.querySelector('h6');
            if (categoryHeader) {
                categories.add(categoryHeader.textContent.trim());
            }
        }
    });
    
    document.getElementById('category-count').textContent = categories.size;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Update selected permissions for any pre-checked boxes (old input)
    updateSelectedPermissions();
    updateSummary();
    
    // Add event listeners to all checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updatePermissionCard(this);
            updateSelectedPermissions();
            updateSummary();
        });
        
        // Initialize card state for pre-checked items
        if (checkbox.checked) {
            updatePermissionCard(checkbox);
        }
    });
    
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
        
        // Confirm if creating super admin role
        const hasAll = Array.from(checkedBoxes).some(cb => cb.value === 'all');
        if (hasAll && !confirm('You are creating a Super Admin role with full system access. Are you sure?')) {
            e.preventDefault();
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