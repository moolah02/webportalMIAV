<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Employee list
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

    // Show onboarding form
    public function create()
    {
        $managers = Employee::where('status', 'active')
            ->whereHas('role', function($query) {
                $query->where('name', 'like', '%manager%')
                      ->orWhere('name', 'like', '%admin%')
                      ->orWhereJsonContains('permissions', 'manage_team')
                      ->orWhereJsonContains('permissions', 'all');
            })
            ->get();

        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.create', compact('managers', 'roles', 'departments'));
    }

    // Store new employee
    // Add this to your EmployeeController store method for debugging
public function store(Request $request)
{
    // Log the incoming data
    \Log::info('Employee creation attempt:', $request->all());
    
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email',
        'password' => ['required', 'confirmed'],
        // Add other validation rules...
    ]);

    try {
        // Generate employee number if missing
        $employeeNumber = $this->generateEmployeeNumber($request->department_id ?? 1);
        
        $employee = Employee::create([
            'employee_number' => $employeeNumber,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
            'status' => $request->status ?? 'active',
            'hire_date' => $request->hire_date ?? now(),
            'phone' => $request->phone,
            // Add other fields...
        ]);

        \Log::info('Employee created successfully:', ['id' => $employee->id, 'email' => $employee->email]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee onboarded successfully! Employee Number: ' . $employeeNumber);

    } catch (\Exception $e) {
        \Log::error('Employee creation failed:', ['error' => $e->getMessage(), 'data' => $request->all()]);
        
        return back()
            ->with('error', 'Failed to create employee: ' . $e->getMessage())
            ->withInput();
    }
}

private function generateEmployeeNumber($departmentId)
{
    $prefix = 'EMP';
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
}
    // Show employee details
    public function show(Employee $employee)
    {
        $employee->load(['role', 'department', 'manager', 'subordinates']);
        return view('employees.show', compact('employee'));
    }

    // Edit employee
    public function edit(Employee $employee)
    {
        $managers = Employee::where('status', 'active')
            ->where('id', '!=', $employee->id)
            ->whereHas('role', function($query) {
                $query->where('name', 'like', '%manager%')
                      ->orWhere('name', 'like', '%admin%')
                      ->orWhereJsonContains('permissions', 'manage_team')
                      ->orWhereJsonContains('permissions', 'all');
            })
            ->get();

        $roles = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('employees.edit', compact('employee', 'managers', 'roles', 'departments'));
    }

    // Update employee
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
        ]);

        try {
            $employee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department_id' => $request->department_id,
                'role_id' => $request->role_id,
                'manager_id' => $request->manager_id,
                'status' => $request->status,
                'hire_date' => $request->hire_date,
                'time_zone' => $request->time_zone ?? 'UTC',
                'language' => $request->language ?? 'en',
                'two_factor_enabled' => $request->has('two_factor_enabled'),
            ]);

            return redirect()->route('employees.index')
                ->with('success', 'Employee updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Generate employee number
   

    // Quick role assignment
    public function updateRole(Request $request, Employee $employee)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee->update(['role_id' => $request->role_id]);

        return response()->json([
            'success' => true, 
            'message' => 'Role updated successfully!',
            'role_name' => $employee->role->name
        ]);
    }

    // Activate/Deactivate employee
    public function toggleStatus(Employee $employee)
    {
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        $employee->update(['status' => $newStatus]);

        return back()->with('success', 'Employee status updated to ' . $newStatus);
    }
}