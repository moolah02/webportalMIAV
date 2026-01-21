<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
 use Illuminate\Support\Arr;
 use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;



class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Employee::with(['roles', 'department', 'manager']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $employees = $query->latest()->paginate(15);

        $departments = Department::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'new_this_month' => Employee::whereMonth('hire_date', now()->month)->count(),
            'pending_onboarding' => Employee::where('status', 'pending')->count(),
        ];

        return view('employees.index', compact('employees', 'departments', 'roles', 'stats'));
    }
private function filterExistingColumns(string $table, array $data): array
{
    $existing = Schema::getColumnListing($table);           // ['id','first_name',...]
    return array_intersect_key($data, array_flip($existing)); // keep only existing keys
}


public function create()
{
    try {
        // Fix: Get managers without using scopes that don't exist
        $managers = Employee::where('status', 'active')
            ->whereHas('roles', function($query) {
                $query->where('name', 'like', '%manager%')
                      ->orWhere('name', 'like', '%admin%')
                      ->orWhereJsonContains('permissions', 'manage_team')
                      ->orWhereJsonContains('permissions', 'all');
            })
            ->get();

        // Get all roles
        $roles = Role::orderBy('name')->get();

        // Get departments, create basic ones if none exist
        $departments = Department::orderBy('name')->get();

        if ($departments->count() === 0) {
            $basicDepartments = ['IT', 'HR', 'Sales', 'Marketing', 'Finance', 'Operations'];
            foreach ($basicDepartments as $deptName) {
                Department::create(['name' => $deptName]);
            }
            $departments = Department::orderBy('name')->get();
        }

        return view('employees.create', compact('managers', 'roles', 'departments'));

    } catch (\Exception $e) {
        Log::error('Error loading employee create form:', ['error' => $e->getMessage()]);

        // Fallback: return with empty collections if there's an error
        return view('employees.create', [
            'managers' => collect([]),
            'roles' => Role::all(),
            'departments' => Department::all()
        ]);
    }
}
// ENHANCED STORE METHOD - Replace your existing store() method
public function store(Request $request)
{
    Log::info('Employee creation attempt started:', [
        'request_data' => $request->except(['password', 'password_confirmation']),
        'has_password' => $request->filled('password'),
        'has_password_confirmation' => $request->filled('password_confirmation')
    ]);

    $validatedData = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:employees,email',
        'password'   => 'required|string|min:6|confirmed',
        'role_id'    => 'required|exists:roles,id',
        'additional_roles' => 'nullable|array',
        'additional_roles.*' => 'exists:roles,id',
        'department_id' => 'required|exists:departments,id',
        'hire_date'  => 'required|date',
        'status'     => 'required|in:active,inactive,pending',
        'phone'      => 'nullable|string|max:20',
        'manager_id' => 'nullable|exists:employees,id',
        'position'   => 'nullable|string|max:255',
        'salary'     => 'nullable|numeric|min:0',
        'address'    => 'nullable|string|max:500',
        'city'       => 'nullable|string|max:255',
        'state'      => 'nullable|string|max:255',
        'country'    => 'nullable|string|max:255',
        'postal_code'=> 'nullable|string|max:20',
        'emergency_contact_name'  => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'emergency_contact_relationship' => 'nullable|string|in:spouse,parent,sibling,child,friend,other',
        'skills'     => 'nullable|string|max:1000',
        'work_location' => 'nullable|string|max:255',
        'avatar'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'notes'      => 'nullable|string|max:1000',
        'time_zone'  => 'nullable|string|max:255',
        'language'   => 'nullable|string|max:10',
        'two_factor_enabled' => 'nullable|boolean',
    ]);

    return DB::transaction(function () use ($validatedData, $request) {

        // Generate a fresh, unique employee number INSIDE the transaction
        $employeeNumber = $this->generateEmployeeNumber($validatedData['department_id']);

        // Get department name for the department field
        $department = \App\Models\Department::find($validatedData['department_id']);
        $departmentName = $department ? $department->name : null;

        // Handle avatar file upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // Process skills - convert comma-separated string to JSON array
        $skillsArray = null;
        if (!empty($validatedData['skills'])) {
            $skillsArray = array_map('trim', explode(',', $validatedData['skills']));
        }

        $employeeData = [
            'employee_id'     => $employeeNumber, // Same as employee_number for consistency
            'employee_number' => $employeeNumber,
            'first_name'  => $validatedData['first_name'],
            'last_name'   => $validatedData['last_name'],
            'email'       => $validatedData['email'],
            'password'    => Hash::make($validatedData['password']),
            'role_id'     => $validatedData['role_id'],
            'department'  => $departmentName,
            'department_id'=> $validatedData['department_id'],
            'status'      => $validatedData['status'] ?? 'active',
            'hire_date'   => $validatedData['hire_date'] ?? now(),
            'phone'       => $validatedData['phone'] ?? null,
            'manager_id'  => $validatedData['manager_id'] ?? null,
            'position'    => $validatedData['position'] ?? null,
            'salary'      => $validatedData['salary'] ?? null,
            'address'     => $validatedData['address'] ?? null,
            'city'        => $validatedData['city'] ?? null,
            'state'       => $validatedData['state'] ?? null,
            'country'     => $validatedData['country'] ?? null,
            'postal_code' => $validatedData['postal_code'] ?? null,
            'emergency_contact_name'  => $validatedData['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validatedData['emergency_contact_phone'] ?? null,
            'emergency_contact_relationship' => $validatedData['emergency_contact_relationship'] ?? null,
            'skills'      => $skillsArray,
            'work_location' => $validatedData['work_location'] ?? null,
            'avatar_url'  => $avatarPath,
            'notes'       => $validatedData['notes'] ?? null,
            'time_zone'   => $validatedData['time_zone'] ?? 'UTC',
            'language'    => $validatedData['language'] ?? 'en',
            'two_factor_enabled' => isset($validatedData['two_factor_enabled']) ? (bool)$validatedData['two_factor_enabled'] : false,
        ];

        // Keep only columns that actually exist
        $employeeData = $this->filterExistingColumns('employees', $employeeData);

        $logData = $employeeData; unset($logData['password']);
        Log::info('Creating employee with data:', $logData);

        // ✅ CREATE ONCE
        $employee = Employee::create($employeeData);

        // Assign primary role
        $employee->roles()->attach($validatedData['role_id']);

        // Assign additional roles if provided
        if ($request->has('additional_roles') && is_array($request->additional_roles)) {
            foreach ($request->additional_roles as $additionalRoleId) {
                // Avoid duplicate - only attach if not the primary role
                if ($additionalRoleId != $validatedData['role_id']) {
                    $employee->roles()->attach($additionalRoleId);
                }
            }
        }

        Log::info('Employee created with multiple roles:', [
            'id' => $employee->id,
            'email' => $employee->email,
            'employee_number' => $employee->employee_number,
            'roles' => $employee->roles->pluck('name')->toArray()
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee created successfully! Employee Number: {$employeeNumber}");
    });
}





   private function generateEmployeeNumber($departmentId): string
{
    // Use the department name to form a 3-letter prefix (fallback EMP)
    $department = Department::find($departmentId);
    $prefix = strtoupper(substr(($department->name ?? 'EMP'), 0, 3)); // e.g. HR
    $year   = now()->format('y');                                     // e.g. 25
    $base   = $prefix.$year;                                          // e.g. HR25

    // Ask MySQL for the largest 4-digit suffix already used for this base
    $max = Employee::where('employee_number', 'like', $base.'%')
        ->selectRaw("MAX(CAST(RIGHT(employee_number, 4) AS UNSIGNED)) as max_suffix")
        ->value('max_suffix');

    // Next available number: 0001, 0002, ...
    $next = str_pad((int)($max ?? 0) + 1, 4, '0', STR_PAD_LEFT);

    return $base.$next; // e.g. HR250001
}


    public function show(Employee $employee)
    {
        $employee->load(['roles', 'department', 'manager', 'subordinates']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $managers = Employee::managers()->active()
            ->where('id', '!=', $employee->id)
            ->get();

        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'managers', 'roles', 'departments'));
    }

public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:employees,email,' . $employee->id,
        'phone'      => 'nullable|string|max:20',
        'department_id' => 'required|exists:departments,id',
        'role_id'    => 'required|exists:roles,id',
        'additional_roles' => 'nullable|array',
        'additional_roles.*' => 'exists:roles,id',
        'manager_id' => 'nullable|exists:employees,id',
        'status'     => 'required|in:active,inactive,pending',
        'hire_date'  => 'required|date',
        'time_zone'  => 'nullable|string|max:255',
        'language'   => 'nullable|string|max:10',
        'two_factor_enabled' => 'boolean',
        'position'   => 'nullable|string|max:255',
        'salary'     => 'nullable|numeric|min:0',
        'password'   => 'nullable|string|min:6|confirmed',
    ]);

    return DB::transaction(function () use ($validated, $request, $employee) {

        $payload = [
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'department_id' => $validated['department_id'],
            'role_id'    => $validated['role_id'],
            'manager_id' => $validated['manager_id'] ?? null,
            'status'     => $validated['status'],
            'hire_date'  => $validated['hire_date'],
            'time_zone'  => $validated['time_zone'] ?? 'UTC',
            'language'   => $validated['language'] ?? 'en',
            'two_factor_enabled' => isset($validated['two_factor_enabled']) ? (bool)$validated['two_factor_enabled'] : false,
            'position'   => $validated['position'] ?? null,
            'salary'     => $validated['salary'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        // Keep only columns that exist
        $payload = $this->filterExistingColumns('employees', $payload);

        // ✅ UPDATE ONCE
        $employee->update($payload);

        // Sync roles: Build complete list of role IDs from primary + additional
        $roleIds = [$validated['role_id']];

        if ($request->has('additional_roles') && is_array($request->additional_roles)) {
            foreach ($request->additional_roles as $additionalRoleId) {
                if ($additionalRoleId != $validated['role_id']) {
                    $roleIds[] = $additionalRoleId;
                }
            }
        }

        // Sync all roles at once (removes old, adds new)
        $employee->roles()->sync($roleIds);

        Log::info('Employee updated with multiple roles:', [
            'id' => $employee->id,
            'email' => $employee->email,
            'roles' => $employee->fresh()->roles->pluck('name')->toArray()
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    });
}



    public function updateRole(Request $request, Employee $employee)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee->update(['role_id' => $request->role_id]);
        $employee->load('role'); // Reload to get updated computed properties

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully!' . ($employee->isFieldTechnician() ? ' (Field Technician)' : ''),
            'role_name' => $employee->role->name,
            'is_technician' => $employee->isFieldTechnician(),
            'specialization' => $employee->getSpecialization()
        ]);
    }

    public function toggleStatus(Employee $employee)
    {
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);

        return back()->with('success', 'Employee status updated to ' . $newStatus);
    }

    // ADD THESE METHODS TO YOUR EXISTING EmployeeController.php

    /**
     * Show the employee's profile page
     */
    public function profile()
    {
         $employee = Auth::user()->load([
            'roles',
            'department',
            'manager.department',
            'subordinates.roles',
            'currentAssetAssignments.asset',
            'assetRequests' => function($query) {
                $query->latest()->take(5);
            }
        ]);

        // Get statistics
        $stats = [
            'total_asset_requests' => $employee->assetRequests()->count(),
            'pending_requests' => $employee->assetRequests()->where('status', 'pending')->count(),
            'assigned_assets_count' => $employee->currentAssetAssignments()->count(),
            'assigned_assets_value' => $employee->assigned_assets_value ?? 0,
            'subordinates_count' => $employee->subordinates()->count(),
        ];

        return view('employee.profile', compact('employee', 'stats'));
    }

    /**
     * Show the form for editing the employee's profile
     */
    public function editProfile()
    {
        $employee = Auth::user()->load(['role', 'department']);

        return view('employee.edit-profile', compact('employee'));
    }

    /**
     * Update the employee's profile
     */
    public function updateProfile(Request $request)
    {
        $employee = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'time_zone' => 'required|string|max:255',
            'language' => 'required|string|max:255',
        ]);

        $employee->update($request->only([
            'first_name',
            'last_name',
            'phone',
            'time_zone',
            'language'
        ]));

        return redirect()->route('employee.profile')
                        ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the employee's password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = Auth::user();

        if (!Hash::check($request->current_password, $employee->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $employee->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('employee.profile')
                        ->with('success', 'Password updated successfully!');
    }
}
