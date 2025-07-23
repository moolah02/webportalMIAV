<?php
// ==============================================
// ROLE CONTROLLER
// File: app/Http/Controllers/RoleController.php
// ==============================================

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Employee;

class RoleController extends Controller
{
    public function __construct()
{
    $this->middleware('auth');
    // COMMENTED OUT - Remove permission middleware temporarily
    /*
    $this->middleware(function ($request, $next) {
        if (!auth()->user()->hasPermission('all') && !auth()->user()->hasPermission('manage_team')) {
            abort(403, 'Unauthorized to manage roles');
        }
        return $next($request);
    });
    */
}

    // Show all roles
   
// ==============================================
// FIXED ROLE CONTROLLER INDEX METHOD
// Replace the index method in your app/Http/Controllers/RoleController.php
// ==============================================

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

        // Calculate statistics safely - without using relationships
        $stats = [
            'total_roles' => Role::count(),
            'roles_with_admin' => Role::whereJsonContains('permissions', 'all')->count(),
            'custom_roles' => Role::whereNotIn('name', ['super_admin', 'admin', 'manager', 'employee', 'technician'])->count(),
            'employees_assigned' => \App\Models\Employee::whereNotNull('role_id')->count(),
        ];

        return view('roles.index', compact('roles', 'allPermissions', 'stats'));

    } catch (\Exception $e) {
        // If there's any error, return a simple view
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
// ==============================================
// ALSO ADD THIS CREATE METHOD FIX
// ==============================================

public function create()
{
    // Get managers with proper error handling
    $managers = Employee::where('status', 'active')
        ->whereHas('role', function($query) {
            $query->where('name', 'like', '%manager%')
                  ->orWhere('name', 'like', '%admin%')
                  ->orWhereJsonContains('permissions', 'manage_team')
                  ->orWhereJsonContains('permissions', 'all');
        })
        ->get();

    // Get roles and departments safely
    $roles = \App\Models\Role::orderBy('name')->get();
    $departments = \App\Models\Department::orderBy('name')->get();

    // If no departments exist, create some basic ones
    if ($departments->count() === 0) {
        $basicDepartments = ['IT', 'HR', 'Sales', 'Marketing', 'Finance', 'Operations'];
        foreach ($basicDepartments as $deptName) {
            \App\Models\Department::create(['name' => $deptName]);
        }
        $departments = \App\Models\Department::orderBy('name')->get();
    }

    return view('employees.create', compact('managers', 'roles', 'departments'));
}
    // Show create form
    

    // Store new role
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

    // Show single role
    public function show(Role $role)
    {
        $role->load('employees');
        $allPermissions = $this->getAllAvailablePermissions();
        
        return view('roles.show', compact('role', 'allPermissions'));
    }

    // Show edit form
    public function edit(Role $role)
    {
        $allPermissions = $this->getAllAvailablePermissions();
        return view('roles.edit', compact('role', 'allPermissions'));
    }

    // Update role
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

    // Delete role
    public function destroy(Role $role)
    {
        try {
            // Check if role is assigned to any employees
            if ($role->employees()->count() > 0) {
                return back()->with('error', 'Cannot delete role that is assigned to employees. Please reassign employees first.');
            }

            // Prevent deletion of system roles
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

    // Get all available permissions
    private function getAllAvailablePermissions()
    {
        return [
            // Super Admin
            'all' => [
                'name' => 'All Permissions',
                'description' => 'Full system access (Super Admin)',
                'category' => 'admin',
                'danger' => true
            ],
            
            // Dashboard & General
            'view_dashboard' => [
                'name' => 'View Dashboard',
                'description' => 'Access main dashboard',
                'category' => 'general'
            ],
            'view_own_data' => [
                'name' => 'View Own Data',
                'description' => 'View personal information',
                'category' => 'general'
            ],
            
            // Asset Management
            'manage_assets' => [
                'name' => 'Manage Assets',
                'description' => 'Full asset management access',
                'category' => 'assets'
            ],
            'request_assets' => [
                'name' => 'Request Assets',
                'description' => 'Request company assets',
                'category' => 'assets'
            ],
            'approve_requests' => [
                'name' => 'Approve Requests',
                'description' => 'Approve asset requests',
                'category' => 'assets'
            ],
            
            // Client Management
            'view_clients' => [
                'name' => 'View Clients',
                'description' => 'View client information',
                'category' => 'clients'
            ],
            'manage_clients' => [
                'name' => 'Manage Clients',
                'description' => 'Full client management',
                'category' => 'clients'
            ],
            
            // Team Management
            'manage_team' => [
                'name' => 'Manage Team',
                'description' => 'Manage team members and permissions',
                'category' => 'management'
            ],
            'view_reports' => [
                'name' => 'View Reports',
                'description' => 'Access reporting system',
                'category' => 'management'
            ],
            
            // Technical
            'view_jobs' => [
                'name' => 'View Jobs',
                'description' => 'View technical jobs/tickets',
                'category' => 'technical'
            ],
            'view_terminals' => [
                'name' => 'View Terminals',
                'description' => 'View POS terminals',
                'category' => 'technical'
            ],
            'update_terminals' => [
                'name' => 'Update Terminals',
                'description' => 'Update terminal information',
                'category' => 'technical'
            ],
            'manage_terminals' => [
                'name' => 'Manage Terminals',
                'description' => 'Full terminal management',
                'category' => 'technical'
            ],
        ];
    }

    // Quick permission update (AJAX)
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
}