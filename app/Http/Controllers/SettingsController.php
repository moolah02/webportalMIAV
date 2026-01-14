<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Asset;
use App\Models\PosTerminal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        // Get stats
        $stats = [
            'total_categories' => $categories->sum(fn($cat) => $cat->count()),
            'asset_categories' => $categories->get('asset_category', collect())->count(),
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

    // ==============================================
    // ROLE MANAGEMENT (Settings area) - delegates to Role model
    // ==============================================
    public function manageRoles()
    {
        $roles = \App\Models\Role::orderBy('name')->get();

        $raw = (new \App\Http\Controllers\RoleController())->getAllAvailablePermissions();
        // Group permissions by category for the settings UI
        $availablePermissions = [];
        foreach ($raw as $key => $meta) {
            $group = $meta['category'] ?? 'other';
            $groupLabel = ucwords(str_replace('_', ' ', $group));
            if (!isset($availablePermissions[$groupLabel])) {
                $availablePermissions[$groupLabel] = [];
            }
            $availablePermissions[$groupLabel][$key] = $meta['name'];
        }

        return view('settings.manage-role', compact('roles', 'availablePermissions'));
    }

    public function storeRole(Request $request)
    {
        $available = array_keys((new \App\Http\Controllers\RoleController())->getAllAvailablePermissions());
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:'.implode(',', $available),
        ]);

        \App\Models\Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'] ?? $validated['name'],
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
            'is_active' => true,
        ]);

        return redirect()->route('settings.roles.manage')->with('success', 'Role created successfully.');
    }

    public function updateRole(Request $request, \App\Models\Role $role)
    {
        $available = array_keys((new \App\Http\Controllers\RoleController())->getAllAvailablePermissions());
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:'.implode(',', $available),
            'is_active' => 'nullable|boolean',
        ]);

        $role->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'] ?? $role->display_name,
            'description' => $validated['description'] ?? $role->description,
            'permissions' => $validated['permissions'] ?? $role->permissions,
            'is_active' => isset($validated['is_active']) ? (bool)$validated['is_active'] : $role->is_active,
        ]);

        return redirect()->route('settings.roles.manage')->with('success', 'Role updated successfully.');
    }

    public function deleteRole(\App\Models\Role $role)
    {
        if ($role->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role assigned to employees.');
        }

        $systemRoles = ['super_admin', 'admin', 'manager', 'employee', 'technician'];
        if (in_array(strtolower($role->name), $systemRoles)) {
            return redirect()->back()->with('error', 'Cannot delete system role.');
        }

        $role->delete();
        return redirect()->route('settings.roles.manage')->with('success', 'Role deleted.');
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
}
