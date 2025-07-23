<?php

// ==============================================
// 3. TECHNICIAN CONTROLLER
// File: app/Http/Controllers/TechnicianController.php
// ==============================================

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Employee;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active.employee']);
        $this->middleware('permission:manage_team,all');
    }

    public function index(Request $request)
    {
        $query = Technician::with(['employee.department', 'employee.role']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // Availability filter
        if ($request->filled('availability')) {
            $query->where('availability_status', $request->availability);
        }

        // Region filter
        if ($request->filled('region')) {
            $query->whereJsonContains('regions', $request->region);
        }

        $technicians = $query->latest()->paginate(15);
        
        $stats = [
            'total_technicians' => Technician::count(),
            'available_technicians' => Technician::available()->count(),
            'busy_technicians' => Technician::busy()->count(),
            'off_duty_technicians' => Technician::where('availability_status', 'off_duty')->count(),
        ];

        return view('technicians.index', compact('technicians', 'stats'));
    }

    public function show(Technician $technician)
    {
        $technician->load([
            'employee', 
            'jobAssignments.posTerminal.client', 
            'serviceReports',
            'assignedTickets'
        ]);
        
        $stats = [
            'todays_jobs' => $technician->todays_jobs_count,
            'pending_jobs' => $technician->pending_jobs_count,
            'completed_jobs' => $technician->jobAssignments()->where('status', 'completed')->count(),
            'tickets_assigned' => $technician->assignedTickets()->where('status', '!=', 'resolved')->count(),
        ];

        return view('technicians.show', compact('technician', 'stats'));
    }

    public function create()
    {
        // Get employees that are not already technicians
        $employees = Employee::whereDoesntHave('technician')
            ->whereHas('role', function($query) {
                $query->where('name', 'Technician');
            })
            ->orderBy('first_name')
            ->get();
            
        $regions = ['North', 'South', 'East', 'West', 'Central'];
        $specializations = [
            'POS Repair',
            'Network Setup', 
            'Software Installation',
            'Hardware Maintenance',
            'Training',
            'Troubleshooting'
        ];

        return view('technicians.create', compact('employees', 'regions', 'specializations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:technicians,employee_id',
            'employee_code' => 'nullable|string|max:50',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string',
            'regions' => 'required|array|min:1',
            'regions.*' => 'string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $technician = Technician::create($validated);

        return redirect()->route('technicians.show', $technician)
            ->with('success', 'Technician profile created successfully.');
    }

    public function edit(Technician $technician)
    {
        $regions = ['North', 'South', 'East', 'West', 'Central'];
        $specializations = [
            'POS Repair',
            'Network Setup',
            'Software Installation', 
            'Hardware Maintenance',
            'Training',
            'Troubleshooting'
        ];

        return view('technicians.edit', compact('technician', 'regions', 'specializations'));
    }

    public function update(Request $request, Technician $technician)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:50',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'string',
            'regions' => 'required|array|min:1', 
            'regions.*' => 'string',
            'availability_status' => 'required|in:available,busy,off_duty',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $technician->update($validated);

        return redirect()->route('technicians.show', $technician)
            ->with('success', 'Technician profile updated successfully.');
    }

    public function updateAvailability(Request $request, Technician $technician)
    {
        $validated = $request->validate([
            'availability_status' => 'required|in:available,busy,off_duty',
        ]);

        $technician->update($validated);

        return back()->with('success', 'Availability status updated successfully.');
    }
}
