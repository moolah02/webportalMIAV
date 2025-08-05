<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Employee::with(['role', 'department', 'manager']);

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

    public function create()
    {
        $managers = Employee::managers()->active()->get();
        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.create', compact('managers', 'roles', 'departments'));
    }

    public function store(Request $request)
    {
        \Log::info('Employee creation attempt:', $request->all());
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'password' => ['required', 'confirmed'],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,inactive,pending',
            'phone' => 'nullable|string|max:20',
            'manager_id' => 'nullable|exists:employees,id',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'time_zone' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'two_factor_enabled' => 'boolean',
        ]);

        try {
            $employeeNumber = $this->generateEmployeeNumber($request->department_id);
            
            // Simple creation - role handles all the logic automatically
            $employee = Employee::create([
                'employee_number' => $employeeNumber,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id, // This is all we need!
                'department_id' => $request->department_id,
                'status' => $request->status ?? 'active',
                'hire_date' => $request->hire_date ?? now(),
                'phone' => $request->phone,
                'manager_id' => $request->manager_id,
                'position' => $request->position,
                'salary' => $request->salary,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'notes' => $request->notes,
                'time_zone' => $request->time_zone ?? 'UTC',
                'language' => $request->language ?? 'en',
                'two_factor_enabled' => $request->has('two_factor_enabled'),
            ]);

            // Load role to get computed properties
            $employee->load('role');

            \Log::info('Employee created successfully:', [
                'id' => $employee->id, 
                'email' => $employee->email,
                'role' => $employee->role->name,
                'is_technician' => $employee->isFieldTechnician(),
                'specialization' => $employee->getSpecialization()
            ]);

            $message = 'Employee onboarded successfully! Employee Number: ' . $employeeNumber;
            if ($employee->isFieldTechnician()) {
                $message .= ' (Field Technician: ' . $employee->getSpecialization() . ')';
            }

            return redirect()->route('employees.index')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Employee creation failed:', ['error' => $e->getMessage(), 'data' => $request->all()]);
            
            return back()
                ->with('error', 'Failed to create employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateEmployeeNumber($departmentId)
    {
        try {
            $department = Department::find($departmentId);
            $prefix = $department ? strtoupper(substr($department->name, 0, 3)) : 'EMP';
            $year = date('y');
            
            $lastEmployee = Employee::where('employee_number', 'like', $prefix . $year . '%')
                ->orderBy('employee_number', 'desc')
                ->first();

            if ($lastEmployee) {
                $lastNumber = (int) substr($lastEmployee->employee_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            return $prefix . $year . $newNumber;
            
        } catch (\Exception $e) {
            \Log::error('Error generating employee number:', ['department_id' => $departmentId, 'error' => $e->getMessage()]);
            return 'EMP' . date('y') . '0001';
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['role', 'department', 'manager', 'subordinates']);
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
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive,pending',
            'hire_date' => 'required|date',
            'time_zone' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:10',
            'two_factor_enabled' => 'boolean',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
        ]);

        try {
            // Simple update - role handles everything automatically
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'role_id' => $request->role_id, // Role change automatically updates technician status
                'manager_id' => $request->manager_id,
                'status' => $request->status,
                'hire_date' => $request->hire_date,
                'time_zone' => $request->time_zone ?? 'UTC',
                'language' => $request->language ?? 'en',
                'two_factor_enabled' => $request->has('two_factor_enabled'),
                'position' => $request->position,
                'salary' => $request->salary,
            ]);

            $message = 'Employee updated successfully!';
            if ($employee->isFieldTechnician()) {
                $message .= ' (Field Technician: ' . $employee->getSpecialization() . ')';
            }

            return redirect()->route('employees.index')->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update employee: ' . $e->getMessage())
                ->withInput();
        }
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
}