import os

base = r'c:\xampp4\htdocs\dashboard\Revival_Technologies\resources\views'

TITLES = {
    r'admin\employees.blade.php':                       'Admin: Employees',
    r'admin\settings.blade.php':                        'Admin: Settings',
    r'asset-approvals\index.blade.php':                 'Asset Approvals',
    r'asset-approvals\show.blade.php':                  'Approval Details',
    r'asset-requests\cart.blade.php':                   'Asset Cart',
    r'asset-requests\catalog.blade.php':                'Asset Catalog',
    r'asset-requests\checkout.blade.php':               'Asset Checkout',
    r'asset-requests\index.blade.php':                  'My Asset Requests',
    r'asset-requests\show.blade.php':                   'Request Details',
    r'assets\create.blade.php':                         'Add Asset',
    r'assets\edit.blade.php':                           'Edit Asset',
    r'assets\show.blade.php':                           'Asset Details',
    r'audit\index.blade.php':                           'Audit Trail',
    r'business-licenses\create.blade.php':              'Add Business License',
    r'business-licenses\edit.blade.php':                'Edit Business License',
    r'business-licenses\index.blade.php':               'Business Licenses',
    r'business-licenses\renew.blade.php':               'Renew License',
    r'business-licenses\show.blade.php':                'License Details',
    r'client-dashboards\index.blade.php':               'Client Dashboards',
    r'client-dashboards\show.blade.php':                'Client Dashboard',
    r'clients\create.blade.php':                        'Add Client',
    r'clients\edit.blade.php':                          'Edit Client',
    r'clients\show.blade.php':                          'Client Details',
    r'dashboard.blade.php':                             'Company Dashboard',
    r'deployment\hierarchical.blade.php':               'Terminal Deployment',
    r'employee\edit-profile.blade.php':                 'Edit Profile',
    r'employee\profile.blade.php':                      'My Profile',
    r'employees\create.blade.php':                      'Add Employee',
    r'employees\edit.blade.php':                        'Edit Employee',
    r'employees\show.blade.php':                        'Employee Details',
    r'jobs\assignment.blade.php':                       'Job Assignment',
    r'manager\approvals.blade.php':                     'Approvals',
    r'manager\team.blade.php':                          'Team Management',
    r'notifications\index.blade.php':                   'Notifications',
    r'pos-terminals\column-mapping.blade.php':          'Column Mapping',
    r'pos-terminals\create.blade.php':                  'Add POS Terminal',
    r'pos-terminals\edit.blade.php':                    'Edit POS Terminal',
    r'pos-terminals\import.blade.php':                  'Import Terminals',
    r'pos-terminals\index.blade.php':                   'POS Terminals',
    r'pos-terminals\show.blade.php':                    'Terminal Details',
    r'profile\profile.blade.php':                       'My Profile',
    r'projects\closure-reports.blade.php':              'Closure Reports',
    r'projects\completion-reports.blade.php':           'Completion Reports',
    r'projects\completion-success.blade.php':           'Project Complete',
    r'projects\create-improved.blade.php':              'New Project',
    r'projects\create.blade.php':                       'New Project',
    r'projects\edit.blade.php':                         'Edit Project',
    r'projects\index.blade.php':                        'Projects',
    r'reports\builder.blade.php':                       'Report Builder',
    r'reports\history.blade.php':                       'Reports History',
    r'reports\system-dashboard.blade.php':              'System Dashboard',
    r'roles\create.blade.php':                          'Create Role',
    r'roles\edit.blade.php':                            'Edit Role',
    r'roles\index.blade.php':                           'Role Management',
    r'roles\show.blade.php':                            'Role Details',
    r'settings\asset-category-fields\index.blade.php':  'Asset Category Fields',
    r'settings\index.blade.php':                        'System Settings',
    r'settings\manage-category.blade.php':              'Manage Categories',
    r'settings\manage-department.blade.php':            'Manage Departments',
    r'settings\manage-role.blade.php':                  'Manage Roles',
    r'technician\dashboard.blade.php':                  'Technician Dashboard',
    r'tickets\index.blade.php':                         'Support Tickets',
    r'visits\create.blade.php':                         'Log a Visit',
    r'visits\show.blade.php':                           'Visit Details',
}

inject = "@section('title', '{title}')\n"
anchor = "@extends('layouts.app')\n"

fixed = 0
for rel, title in TITLES.items():
    fp = os.path.join(base, rel)
    if not os.path.exists(fp):
        print(f'MISSING FILE: {rel}')
        continue
    with open(fp, encoding='utf-8') as f:
        content = f.read()
    if "@section('title'" in content:
        print(f'Already has title: {rel}')
        continue
    if anchor not in content:
        print(f'No extends anchor: {rel}')
        continue
    new_content = content.replace(anchor, anchor + inject.format(title=title), 1)
    with open(fp, 'w', encoding='utf-8', newline='') as f:
        f.write(new_content)
    print(f'Added "{title}" to {rel}')
    fixed += 1

print(f'\nDone: {fixed} files updated')
