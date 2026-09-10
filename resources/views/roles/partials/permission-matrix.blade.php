{{--
    Grouped permission checkboxes for roles/create and roles/edit.
    Expects: $allPermissions (key => [name, description, category, danger?]), $checked (array of keys).
    Icon values in the permission list are intentionally not rendered; groups use sprite icons.
--}}
@php
    $groupedPermissions = collect($allPermissions)->groupBy('category', true);
    $checked = is_array($checked ?? null) ? $checked : [];
    $categoryConfig = [
        'admin'      => ['name' => 'System Administration', 'icon' => 'settings'],
        'dashboard'  => ['name' => 'Dashboard Access',      'icon' => 'grid'],
        'assets'     => ['name' => 'Asset Management',      'icon' => 'box'],
        'operations' => ['name' => 'Field Operations',      'icon' => 'wrench'],
        'clients'    => ['name' => 'Client Management',     'icon' => 'building'],
        'management' => ['name' => 'Employee Management',   'icon' => 'users'],
        'technician' => ['name' => 'Technician Portal',     'icon' => 'user-check'],
        'reports'    => ['name' => 'Reports & Analytics',   'icon' => 'chart'],
        'special'    => ['name' => 'Special Operations',    'icon' => 'zap'],
    ];
@endphp

@foreach($groupedPermissions as $category => $permissions)
@php
    $config = $categoryConfig[$category] ?? ['name' => ucfirst($category), 'icon' => 'layers'];
    $selectedInCategory = count(array_intersect($checked, array_keys($permissions->toArray())));
@endphp
<div class="permission-category">
    <div class="category-header" onclick="toggleCategory('{{ $category }}')">
        <span class="rp-cat-icon"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-{{ $config['icon'] }}"/></svg></span>
        <div>
            <div class="rp-cat-title">{{ $config['name'] }}</div>
            <div class="rp-cat-sub">{{ count($permissions) }} permission{{ count($permissions) > 1 ? 's' : '' }} available</div>
        </div>
        <div class="rp-cat-actions">
            <span class="category-selection-info" id="info-{{ $category }}"><span id="selected-{{ $category }}">{{ $selectedInCategory }}</span>/{{ count($permissions) }} selected</span>
            <button type="button" class="rp-link-btn" onclick="event.stopPropagation(); selectAllInCategory('{{ $category }}')">Select All</button>
            <button type="button" class="rp-link-btn" onclick="event.stopPropagation(); clearAllInCategory('{{ $category }}')">Clear All</button>
            <span class="category-toggle-icon" id="toggle-{{ $category }}" style="transform: rotate(180deg);"><svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-chevron-down"/></svg></span>
        </div>
    </div>

    <div class="category-content expanded" id="category-{{ $category }}" style="max-height: none;">
        <div class="rp-perm-grid">
            @foreach($permissions as $key => $permission)
            @php $isChecked = in_array($key, $checked); @endphp
            <label class="permission-item {{ $isChecked ? 'selected' : '' }} {{ $key === 'all' && $isChecked ? 'danger' : '' }}"
                   id="permission-{{ $key }}" data-category="{{ $category }}">
                <input type="checkbox" name="permissions[]" value="{{ $key }}" {{ $isChecked ? 'checked' : '' }}>
                <span class="rp-perm-text">
                    <span><span class="perm-name">{{ $permission['name'] }}</span>@if(!empty($permission['danger']))<span class="rp-danger-chip">DANGER</span>@endif</span>
                    <span class="perm-desc">{{ $permission['description'] }}</span>
                </span>
            </label>
            @endforeach
        </div>
    </div>
</div>
@endforeach
