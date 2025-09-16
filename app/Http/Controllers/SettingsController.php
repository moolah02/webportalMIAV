<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use App\Models\Asset;
use App\Models\PosTerminal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class SettingsController extends Controller
{
    public function index()
    {
        $categoryTypes = Category::getTypes();
        $categories = Category::with([])
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        // Get stats - Fixed role query
        $stats = [
            'total_categories' => $categories->sum(fn($cat) => $cat->count()),
            'asset_categories' => $categories->get('asset_category', collect())->count(),
            'roles_count' => Role::count(),
            'active_roles' => Role::whereExists(function($query) {
                // Check if roles table has is_active column, if not just count all
                $query->selectRaw('1');
            })->count(),
        ];

        return view('settings.index', compact('categoryTypes', 'categories', 'stats'));
    }

    // Generic category management
    public function manageCategory($type)
    {
        $categoryTypes = Category::getTypes();

        if (!array_key_exists($type, $categoryTypes)) {
            abort(404);
        }

        $categories = Category::ofType($type)->ordered()->get();
        $typeLabel = $categoryTypes[$type];

        return view('settings.manage-category', compact('categories', 'type', 'typeLabel'));
    }

    public function storeCategory(Request $request, $type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        Category::create([
            'type' => $type,
            'name' => $request->name,
            'slug' => $type . '-' . Str::slug($request->name), // Prefix with type to ensure uniqueness
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
            'sort_order' => Category::ofType($type)->max('sort_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $updateData = $request->only(['name', 'description', 'color', 'icon', 'is_active']);

        // Update slug if name changed
        if ($category->name !== $request->name) {
            $updateData['slug'] = $category->type . '-' . Str::slug($request->name);
        }

        $category->update($updateData);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(Category $category)
    {
        $canDelete = $this->canDeleteCategory($category);

        if (!$canDelete['can_delete']) {
            return redirect()->back()->with('error', $canDelete['message']);
        }

        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function updateCategoryOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        foreach ($request->categories as $index => $categoryId) {
            Category::where('id', $categoryId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    // Role Management
    public function manageRoles()
    {
        $roles = Role::orderBy('name')->get();
        $availablePermissions = $this->getAvailablePermissions();

        return view('settings.manage-roles', compact('roles', 'availablePermissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        $roleData = [
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ];

        // Add optional fields only if they exist in the table
        if ($request->display_name) {
            $roleData['display_name'] = $request->display_name;
        }
        if ($request->description) {
            $roleData['description'] = $request->description;
        }

        // Check if is_active column exists
        if (Schema::hasColumn('roles', 'is_active')) {
            $roleData['is_active'] = true;
        }

        Role::create($roleData);

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'permissions' => $request->permissions ?? [],
        ];

        // Add optional fields only if they're provided and exist in the table
        if ($request->has('display_name')) {
            $updateData['display_name'] = $request->display_name;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        if ($request->has('is_active') && Schema::hasColumn('roles', 'is_active')) {
            $updateData['is_active'] = $request->boolean('is_active', true);
        }

        $role->update($updateData);

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function deleteRole(Role $role)
    {
        $employeeCount = $role->employees()->count();

        if ($employeeCount > 0) {
            return redirect()->back()->with('error', "Cannot delete role. It's being used by {$employeeCount} employee(s).");
        }

        $role->delete();
        return redirect()->back()->with('success', 'Role deleted successfully.');
    }

    private function canDeleteCategory(Category $category)
    {
        switch ($category->type) {
            case Category::TYPE_ASSET_CATEGORY:
                $count = Asset::where('category', $category->name)->count();
                return [
                    'can_delete' => $count === 0,
                    'message' => $count > 0 ? "Cannot delete category. It's being used by {$count} asset(s)." : ''
                ];

            case Category::TYPE_TERMINAL_STATUS:
                $count = PosTerminal::where('status', $category->slug)
                    ->orWhere('current_status', $category->slug)
                    ->count();
                return [
                    'can_delete' => $count === 0,
                    'message' => $count > 0 ? "Cannot delete status. It's being used by {$count} terminal(s)." : ''
                ];

            case Category::TYPE_SERVICE_TYPE:
                // Check if job_assignments table exists
                if (Schema::hasTable('job_assignments')) {
                    $count = DB::table('job_assignments')->where('service_type', $category->slug)->count();
                    return [
                        'can_delete' => $count === 0,
                        'message' => $count > 0 ? "Cannot delete service type. It's being used by {$count} job assignment(s)." : ''
                    ];
                }
                return ['can_delete' => true, 'message' => ''];

            default:
                return ['can_delete' => true, 'message' => ''];
        }
    }

    private function getAvailablePermissions()
    {
        return [
            'System' => [
                'all' => 'All Permissions',
                'view_dashboard' => 'View Dashboard',
                'manage_settings' => 'Manage Settings',
            ],
            'Assets' => [
                'manage_assets' => 'Manage Assets',
                'view_assets' => 'View Assets',
                'request_assets' => 'Request Assets',
                'approve_requests' => 'Approve Asset Requests',
            ],
            'Terminals' => [
                'manage_terminals' => 'Manage POS Terminals',
                'view_terminals' => 'View POS Terminals',
                'update_terminals' => 'Update Terminal Status',
            ],
            'Jobs' => [
                'view_jobs' => 'View Job Assignments',
                'manage_jobs' => 'Manage Job Assignments',
                'assign_jobs' => 'Assign Jobs to Technicians',
            ],
            'Team' => [
                'manage_team' => 'Manage Team Members',
                'view_team' => 'View Team Members',
                'manage_roles' => 'Manage Roles & Permissions',
            ],
            'Clients' => [
                'manage_clients' => 'Manage Clients',
                'view_clients' => 'View Clients',
            ],
            'Reports' => [
                'view_reports' => 'View Reports',
                'generate_reports' => 'Generate Reports',
                'export_reports' => 'Export Reports',
            ],
            'Personal' => [
                'view_own_data' => 'View Own Data',
                'edit_own_profile' => 'Edit Own Profile',
            ],
        ];
    }
}
