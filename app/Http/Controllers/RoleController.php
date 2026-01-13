<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Employee;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $query = Role::query();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            $roles = $query->orderBy('name')->paginate(15);

            // Get all available permissions
            $allPermissions = $this->getAllAvailablePermissions();

            // Calculate statistics
            $stats = [
                'total_roles' => Role::count(),
                'roles_with_admin' => Role::whereJsonContains('permissions', 'all')->count(),
                'custom_roles' => Role::whereNotIn('name', ['super_admin', 'admin', 'manager', 'employee', 'technician'])->count(),
                'employees_assigned' => \App\Models\Employee::whereNotNull('role_id')->count(),
            ];

            return view('roles.index', compact('roles', 'allPermissions', 'stats'));

        } catch (\Exception $e) {
            $roles = Role::all();
            $allPermissions = $this->getAllAvailablePermissions();
            $stats = [
                'total_roles' => $roles->count(),
                'roles_with_admin' => 0,
                'custom_roles' => 0,
                'employees_assigned' => 0,
            ];

            return view('roles.index', compact('roles', 'allPermissions', 'stats'));
        }
    }

    public function create()
    {
        $allPermissions = $this->getAllAvailablePermissions();
        return view('roles.create', compact('allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAllAvailablePermissions())),
        ]);

        try {
            Role::create([
                'name' => $request->name,
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create role: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Role $role)
    {
        $role->load('employees');
        $allPermissions = $this->getAllAvailablePermissions();

        return view('roles.show', compact('role', 'allPermissions'));
    }

    public function edit(Role $role)
    {
        $allPermissions = $this->getAllAvailablePermissions();
        return view('roles.edit', compact('role', 'allPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAllAvailablePermissions())),
        ]);

        try {
            $role->update([
                'name' => $request->name,
                'permissions' => $request->permissions ?? [],
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update role: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            if ($role->employees()->count() > 0) {
                return back()->with('error', 'Cannot delete role that is assigned to employees. Please reassign employees first.');
            }

            $systemRoles = ['super_admin', 'admin', 'manager', 'employee', 'technician'];
            if (in_array($role->name, $systemRoles)) {
                return back()->with('error', 'Cannot delete system role.');
            }

            $role->delete();

            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    public function clone(Role $role)
    {
        try {
            $newRole = Role::create([
                'name' => $role->name . '_copy',
                'permissions' => $role->permissions,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role cloned successfully!',
                'newRoleId' => $newRole->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clone role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->getAllAvailablePermissions())),
        ]);

        try {
            $role->update([
                'permissions' => $request->permissions ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully!',
                'permissions' => $role->permissions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    // COMPLETE PERMISSION SYSTEM - ALL MENU ITEMS COVERED
    public function getAllAvailablePermissions()
    {
        return [
            // ==============================================
            // SUPER ADMIN
            // ==============================================
            'all' => [
                'name' => 'All Permissions (Super Admin)',
                'description' => 'Complete system access - bypasses all permission checks',
                'category' => 'admin',
                'danger' => true,
                'icon' => '⚡'
            ],

            // ==============================================
            // DASHBOARD & GENERAL ACCESS
            // ==============================================
            'view_dashboard' => [
                'name' => 'Company Dashboard',
                'description' => 'Access main company dashboard with system metrics',
                'category' => 'dashboard',
                'icon' => '📊'
            ],
            'view_own_data' => [
                'name' => 'Employee Dashboard',
                'description' => 'Access personal employee dashboard and profile',
                'category' => 'dashboard',
                'icon' => '👤'
            ],

            // ==============================================
            // ASSET MANAGEMENT
            // ==============================================
            'manage_assets' => [
                'name' => 'Manage Internal Assets',
                'description' => 'Full asset management including create, edit, delete',
                'category' => 'assets',
                'icon' => '🏢'
            ],
            'view_assets' => [
                'name' => 'View Assets',
                'description' => 'View asset inventory and details',
                'category' => 'assets',
                'icon' => '👁️'
            ],
            'manage_terminals' => [
                'name' => 'Manage POS Terminals',
                'description' => 'Full POS terminal management and configuration',
                'category' => 'assets',
                'icon' => '💳'
            ],
            'view_terminals' => [
                'name' => 'View POS Terminals',
                'description' => 'View POS terminal information and status',
                'category' => 'assets',
                'icon' => '👁️'
            ],
            'request_assets' => [
                'name' => 'Request Assets',
                'description' => 'Submit asset requests through catalog',
                'category' => 'assets',
                'icon' => '🛒'
            ],
            'view_own_requests' => [
                'name' => 'View My Requests',
                'description' => 'View personal asset request history',
                'category' => 'assets',
                'icon' => '📋'
            ],
            'approve_requests' => [
                'name' => 'Approve Asset Requests',
                'description' => 'Approve or reject asset requests from employees',
                'category' => 'assets',
                'icon' => '✅'
            ],
            'manage_licenses' => [
                'name' => 'Manage Business Licenses',
                'description' => 'Manage business licenses and compliance',
                'category' => 'assets',
                'icon' => '📄'
            ],

            // ==============================================
            // FIELD OPERATIONS
            // ==============================================
            'manage_deployments' => [
                'name' => 'Terminal Deployment',
                'description' => 'Plan and manage terminal deployments',
                'category' => 'operations',
                'icon' => '🚀'
            ],
            'manage_jobs' => [
                'name' => 'All Job Assignments',
                'description' => 'Create and manage all job assignments',
                'category' => 'operations',
                'icon' => '📋'
            ],
            'assign_jobs' => [
                'name' => 'Assign Jobs',
                'description' => 'Assign jobs to technicians',
                'category' => 'operations',
                'icon' => '👨‍🔧'
            ],
            'view_jobs' => [
                'name' => 'View Jobs',
                'description' => 'View assigned jobs (technician access)',
                'category' => 'operations',
                'icon' => '👁️'
            ],
            'view_technician_reports' => [
                'name' => 'View Technician Reports',
                'description' => 'Access technician visit reports and analytics',
                'category' => 'operations',
                'icon' => '📊'
            ],
            'manage_visits' => [
                'name' => 'Manage Site Visits',
                'description' => 'Create and manage site visit records',
                'category' => 'operations',
                'icon' => '📝'
            ],
            'view_visits' => [
                'name' => 'View Site Visits',
                'description' => 'View site visit information',
                'category' => 'operations',
                'icon' => '👁️'
            ],
            'manage_tickets' => [
                'name' => 'Manage Support Tickets',
                'description' => 'Create, assign and resolve support tickets',
                'category' => 'operations',
                'icon' => '🎫'
            ],
            'view_tickets' => [
                'name' => 'View Support Tickets',
                'description' => 'View support ticket information',
                'category' => 'operations',
                'icon' => '👁️'
            ],

            // ==============================================
            // CLIENT MANAGEMENT
            // ==============================================
            'manage_clients' => [
                'name' => 'Manage Clients',
                'description' => 'Full client management including create, edit, delete',
                'category' => 'clients',
                'icon' => '🏢'
            ],
            'view_clients' => [
                'name' => 'View Clients',
                'description' => 'View client information and details',
                'category' => 'clients',
                'icon' => '👁️'
            ],
            'view_client_dashboards' => [
                'name' => 'Client Dashboards',
                'description' => 'Access client-specific dashboards and metrics',
                'category' => 'clients',
                'icon' => '📊'
            ],

            // ==============================================
            // EMPLOYEE MANAGEMENT
            // ==============================================
            'manage_employees' => [
                'name' => 'Manage Employees',
                'description' => 'Full employee management including hire, edit, deactivate',
                'category' => 'management',
                'icon' => '👥'
            ],
            'view_employees' => [
                'name' => 'View Employees',
                'description' => 'View employee information and profiles',
                'category' => 'management',
                'icon' => '👁️'
            ],
            'manage_roles' => [
                'name' => 'Manage Roles',
                'description' => 'Create and modify user roles and permissions',
                'category' => 'management',
                'icon' => '🔐'
            ],
            'manage_team' => [
                'name' => 'Team Management',
                'description' => 'Manage team operations and workflows',
                'category' => 'management',
                'icon' => '👥'
            ],

            // ==============================================
            // TECHNICIAN PORTAL
            // ==============================================
            'view_schedule' => [
                'name' => 'View Schedule',
                'description' => 'View personal work schedule and appointments',
                'category' => 'technician',
                'icon' => '📅'
            ],
            'create_reports' => [
                'name' => 'Create Service Reports',
                'description' => 'Create and submit service reports',
                'category' => 'technician',
                'icon' => '📝'
            ],
            'view_own_reports' => [
                'name' => 'View My Reports',
                'description' => 'View personal service report history',
                'category' => 'technician',
                'icon' => '📋'
            ],

            // ==============================================
            // ADMINISTRATION
            // ==============================================
            'manage_settings' => [
                'name' => 'System Settings',
                'description' => 'Manage system configuration and settings',
                'category' => 'admin',
                'icon' => '⚙️'
            ],
            'manage_documents' => [
                'name' => 'Manage Documents',
                'description' => 'Upload, organize and manage documents',
                'category' => 'admin',
                'icon' => '📁'
            ],
            'view_documents' => [
                'name' => 'View Documents',
                'description' => 'Access and download documents',
                'category' => 'admin',
                'icon' => '👁️'
            ],

            // ==============================================
            // REPORTS & ANALYTICS
            // ==============================================
            'view_reports' => [
                'name' => 'Reports Dashboard',
                'description' => 'Access reporting dashboard and analytics',
                'category' => 'reports',
                'icon' => '📈'
            ],
            'view_technician_visits' => [
                'name' => 'Technician Visit Reports',
                'description' => 'View detailed technician visit reports',
                'category' => 'reports',
                'icon' => '👨‍🔧'
            ],
            'use_report_builder' => [
                'name' => 'Report Builder',
                'description' => 'Create custom reports using report builder',
                'category' => 'reports',
                'icon' => '🏗️'
            ],
            'export_reports' => [
                'name' => 'Export Reports',
                'description' => 'Export reports to various formats',
                'category' => 'reports',
                'icon' => '📤'
            ],

            // ==============================================
            // SPECIAL PERMISSIONS
            // ==============================================
            'import_data' => [
                'name' => 'Import Data',
                'description' => 'Import data from files (Excel, CSV)',
                'category' => 'special',
                'icon' => '📥'
            ],
            'export_data' => [
                'name' => 'Export Data',
                'description' => 'Export system data to files',
                'category' => 'special',
                'icon' => '📤'
            ],
            'bulk_operations' => [
                'name' => 'Bulk Operations',
                'description' => 'Perform bulk operations on records',
                'category' => 'special',
                'icon' => '🔄'
            ],
            'system_logs' => [
                'name' => 'System Logs',
                'description' => 'View system logs and audit trails',
                'category' => 'special',
                'icon' => '📝'
            ]
        ];
    }
}
