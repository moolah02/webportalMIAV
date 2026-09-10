{{-- Quick permission templates (roles/create, roles/edit) --}}
<div class="ui-card">
    <div class="ui-card-header"><h3>Quick Templates</h3></div>
    <div class="rp-templates">
        <button type="button" onclick="applyTemplate('super_admin')" class="template-btn danger">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-shield"/></svg> Super Administrator
        </button>
        <button type="button" onclick="applyTemplate('department_manager')" class="template-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-briefcase"/></svg> Department Manager
        </button>
        <button type="button" onclick="applyTemplate('team_lead')" class="template-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-users"/></svg> Team Lead
        </button>
        <button type="button" onclick="applyTemplate('field_technician')" class="template-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-wrench"/></svg> Field Technician
        </button>
        <button type="button" onclick="applyTemplate('office_staff')" class="template-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-building"/></svg> Office Staff
        </button>
        <button type="button" onclick="applyTemplate('basic_employee')" class="template-btn">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-user"/></svg> Basic Employee
        </button>
        <div class="rp-sep"></div>
        <button type="button" onclick="clearAll()" class="template-btn clear">
            <svg class="mv-i mv-i-sm" aria-hidden="true"><use href="#i-x"/></svg> Clear All
        </button>
    </div>
</div>
