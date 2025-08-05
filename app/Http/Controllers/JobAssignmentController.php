<?php
// app/Http/Controllers/JobAssignmentController.php

namespace App\Http\Controllers;

use App\Models\JobAssignment;
use App\Models\Employee;
use App\Models\Region;
use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\Category;  // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobAssignmentController extends Controller
{
    /**
     * Display job assignment page
     */
    public function index()
    {
        // Clear any cached data
        \Cache::forget('job_assignment_regions');
        \Cache::forget('job_assignment_technicians');
        
        // Get field technicians - FIXED to filter properly
        $technicians = Employee::active()
            ->whereHas('role', function($query) {
                $query->where('name', 'LIKE', '%technician%')
                      ->orWhere('name', 'LIKE', '%technical%')
                      ->orWhere('name', 'LIKE', '%field%');
            })
            ->select('id', 'first_name', 'last_name', 'phone', 'role_id')
            ->with('role:id,name')
            ->orderBy('first_name')
            ->get()
            ->map(function($technician) {
                $technician->name = $technician->first_name . ' ' . $technician->last_name;
                $technician->specialization = $technician->role->name ?? 'General';
                return $technician;
            });

        // If no technicians found with those role names, get employees from specific role IDs
        if ($technicians->isEmpty()) {
            // Adjust these role IDs based on your actual technician roles
            $technicianRoleIds = [3, 4]; // Update these with your actual technician role IDs
            
            $technicians = Employee::active()
                ->whereIn('role_id', $technicianRoleIds)
                ->select('id', 'first_name', 'last_name', 'phone', 'role_id')
                ->with('role:id,name')
                ->orderBy('first_name')
                ->get()
                ->map(function($technician) {
                    $technician->name = $technician->first_name . ' ' . $technician->last_name;
                    $technician->specialization = $technician->role->name ?? 'General';
                    return $technician;
                });
        }

        \Log::info('Technicians loaded', [
            'technicians_count' => $technicians->count(),
            'technicians' => $technicians->pluck('name', 'id')->toArray()
        ]);

        // Get regions - Use the actual regions table
        $regions = Region::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($region) {
                $region->code = $region->region_code ?? strtoupper(substr($region->name, 0, 3));
                $region->pos_terminals_count = PosTerminal::where('region_id', $region->id)->count();
                return $region;
            });
            
        \Log::info('Regions loaded', ['regions' => $regions->toArray()]);

        $clients = Client::orderBy('company_name')->get();

        // Get service types from categories
        $serviceTypes = Category::getServiceTypesWithDetails();
        
        // Get status options for filtering
        $statusOptions = [
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress', 
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        // Get assignments with relationships - ADD MORE DEBUGGING
        $assignments = JobAssignment::with([
            'technician:id,first_name,last_name,phone,role_id', 
            'technician.role:id,name',
            'region:id,name',
            'client:id,company_name'
        ])
        ->orderBy('created_at', 'desc') // Changed from scheduled_date to created_at
        ->get();
        
        \Log::info('Assignments query result', [
            'total_assignments' => JobAssignment::count(),
            'assignments_with_relations' => $assignments->count(),
            'raw_assignments' => JobAssignment::select('id', 'assignment_id', 'technician_id', 'region_id', 'status', 'created_at')->get()->toArray()
        ]);
        
        // Add computed fields to assignments
        $assignments->transform(function ($assignment) {
            if ($assignment->technician) {
                $assignment->technician->name = $assignment->technician->first_name . ' ' . $assignment->technician->last_name;
                $assignment->technician->specialization = $assignment->technician->role->name ?? 'General';
            }
            
            // Add assignment_number if it doesn't exist
            if (!isset($assignment->assignment_number)) {
                $assignment->assignment_number = $assignment->assignment_id;
            }
            
            return $assignment;
        });
        
        \Log::info('Final assignments for view', [
            'assignments_count' => $assignments->count(),
            'assignment_ids' => $assignments->pluck('assignment_id')->toArray()
        ]);

        // Calculate stats
        $stats = [
            'today_assignments' => JobAssignment::whereDate('scheduled_date', now())->count(),
            'pending_assignments' => JobAssignment::where('status', 'assigned')->count(),
            'in_progress_assignments' => JobAssignment::where('status', 'in_progress')->count(),
            'completed_today' => JobAssignment::where('status', 'completed')
                ->whereDate('updated_at', now())
                ->count()
        ];

        return view('jobs.assignment', compact(
            'technicians', 
            'regions', 
            'clients', 
            'assignments',
            'serviceTypes',
            'statusOptions',
            'stats'
        ));
    }

    /**
     * Get terminals for a region
     */
    
public function getRegionTerminals($regionId, Request $request)
{
    try {
        \Log::info('Getting terminals for region', [
            'region_id' => $regionId,
            'client_id' => $request->get('client_id')
        ]);

        $query = PosTerminal::where('region_id', $regionId)
            ->where('is_active', true); // Add this if you have an active status

        if ($request->get('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }

        $terminals = $query->with(['client:id,company_name'])
            ->select('id', 'terminal_id', 'merchant_name', 'client_id', 'status', 'address', 'physical_address')
            ->orderBy('terminal_id')
            ->get();

        \Log::info('Terminals found', [
            'count' => $terminals->count(),
            'terminals' => $terminals->toArray()
        ]);

        return response()->json([
            'success' => true,
            'terminals' => $terminals,
            'count' => $terminals->count()
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading terminals', [
            'region_id' => $regionId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error loading terminals: ' . $e->getMessage(),
            'terminals' => []
        ], 500);
    }
}
    public function store(Request $request)
{
    \Log::info('Job assignment store request', [
        'all_data' => $request->all(),
        'pos_terminals_raw' => $request->get('pos_terminals')
    ]);

    // Get valid service types from categories
    $validServiceTypes = Category::getSelectOptions(Category::TYPE_SERVICE_TYPE)->keys()->toArray();
    
    // If no service types from categories, use fallback
    if (empty($validServiceTypes)) {
        $validServiceTypes = [
            'routine_maintenance', 'emergency_repair', 'software_update', 
            'hardware_replacement', 'network_configuration', 'installation', 'decommission'
        ];
    }

    $validator = Validator::make($request->all(), [
        'technician_id' => 'required|exists:employees,id',
        'region_id' => 'required|exists:regions,id',
        'pos_terminals' => 'required|string|min:3', // Should be JSON string with at least []
        'client_id' => 'nullable|exists:clients,id',
        'scheduled_date' => 'required|date|after_or_equal:today',
        'service_type' => 'required|in:' . implode(',', $validServiceTypes),
        'priority' => 'required|in:low,normal,high,emergency',
        'estimated_duration_hours' => 'nullable|numeric|min:0.5|max:8',
        'notes' => 'nullable|string|max:1000'
    ]);

    if ($validator->fails()) {
        \Log::error('Validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $request->all()
        ]);

        // Return JSON response for AJAX submissions
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        return redirect()->back()
                        ->withInput()
                        ->withErrors($validator->errors());
    }

    DB::beginTransaction();
    
    try {
        // Decode and validate terminals
        $terminalsJson = $request->get('pos_terminals');
        \Log::info('Decoding terminals JSON', ['json' => $terminalsJson]);
        
        $terminals = json_decode($terminalsJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON in pos_terminals: ' . json_last_error_msg());
        }
        
        if (empty($terminals) || !is_array($terminals)) {
            throw new \Exception('At least one terminal must be selected');
        }

        // Verify terminals exist and belong to the region
        $validTerminals = PosTerminal::where('region_id', $request->region_id)
            ->whereIn('id', $terminals)
            ->pluck('id')
            ->toArray();

        if (count($validTerminals) !== count($terminals)) {
            throw new \Exception('Some selected terminals are invalid or don\'t belong to the selected region');
        }

        // Generate assignment ID
        $assignmentId = JobAssignment::generateAssignmentId();
        
        \Log::info('Creating assignment with validated data', [
            'assignment_id' => $assignmentId,
            'terminals_count' => count($validTerminals),
            'valid_terminals' => $validTerminals
        ]);

        // Create assignment
        $assignment = JobAssignment::create([
            'assignment_id' => $assignmentId,
            'technician_id' => (int) $request->technician_id,
            'region_id' => (int) $request->region_id,
            'client_id' => $request->client_id ? (int) $request->client_id : null,
            'pos_terminals' => $validTerminals, // This will be cast to JSON automatically
            'scheduled_date' => $request->scheduled_date,
            'service_type' => $request->service_type,
            'priority' => $request->priority,
            'status' => 'assigned',
            'estimated_duration_hours' => $request->estimated_duration_hours ? (float) $request->estimated_duration_hours : null,
            'notes' => $request->notes,
            'created_by' => auth()->id() ?? 1 // Fallback if no auth
        ]);

        DB::commit();
        
        \Log::info('Assignment created successfully', [
            'id' => $assignment->id,
            'assignment_id' => $assignment->assignment_id
        ]);

        // Return appropriate response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Job assignment created successfully!',
                'assignment_id' => $assignmentId,
                'redirect' => route('jobs.assignment.index')
            ]);
        }

        return redirect()->route('jobs.assignment.index')
                        ->with('success', 'Job assignment created successfully! Assignment ID: ' . $assignmentId);

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Job assignment creation failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->except(['_token'])
        ]);
        
        $errorMessage = 'Error creating assignment: ' . $e->getMessage();
        
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
        
        return redirect()->back()
                        ->withInput()
                        ->withErrors(['error' => $errorMessage]);
    }
}

public function complete($assignmentId)
{
    try {
        $assignment = JobAssignment::findOrFail($assignmentId);
        
        if (!in_array($assignment->status, ['assigned', 'in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only assigned or in-progress jobs can be completed'
            ], 422);
        }

        $updateData = ['status' => 'completed'];
        
        if (!$assignment->actual_start_time) {
            $updateData['actual_start_time'] = Carbon::now();
        }
        $updateData['actual_end_time'] = Carbon::now();

        $assignment->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Assignment completed successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error completing assignment: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate report - ADD THIS METHOD
 */
public function generateReport($assignmentId)
{
    $assignment = JobAssignment::with([
        'technician', 'region', 'client'
    ])->findOrFail($assignmentId);

    // Get terminal details
    $terminals = PosTerminal::whereIn('id', $assignment->pos_terminals)->get();

    // Generate PDF or return view - implement as needed
    return view('jobs.reports.assignment', compact('assignment', 'terminals'));
}
    public function show($assignmentId)
    {
        $assignment = JobAssignment::with([
            'technician:id,first_name,last_name,phone,specialization',
            'region:id,name',
            'client:id,company_name'
        ])->findOrFail($assignmentId);

        // Add computed name
        if ($assignment->technician) {
            $assignment->technician->name = $assignment->technician->first_name . ' ' . $assignment->technician->last_name;
        }

        // Get terminal details
        $terminals = PosTerminal::whereIn('id', $assignment->pos_terminals)
            ->with('client')
            ->get();

        // NEW: Get service type category details
        $serviceTypeCategory = Category::findBySlugAndType($assignment->service_type, Category::TYPE_SERVICE_TYPE);

        $html = view('jobs.partials.assignment-details', compact('assignment', 'terminals', 'serviceTypeCategory'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Update assignment status
     */
    public function updateStatus($assignmentId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:assigned,in_progress,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $assignment = JobAssignment::findOrFail($assignmentId);
            
            $updateData = ['status' => $request->status];
            
            // Add timestamps based on status
            if ($request->status === 'in_progress' && $assignment->status === 'assigned') {
                $updateData['actual_start_time'] = Carbon::now();
            } elseif ($request->status === 'completed') {
                if (!$assignment->actual_start_time) {
                    $updateData['actual_start_time'] = Carbon::now();
                }
                $updateData['actual_end_time'] = Carbon::now();
            }

            $assignment->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Assignment status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel assignment
     */
    public function cancel($assignmentId)
    {
        try {
            $assignment = JobAssignment::findOrFail($assignmentId);
            
            if ($assignment->status !== 'assigned') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only assigned jobs can be cancelled'
                ], 422);
            }

            $assignment->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Assignment cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export assignments
     */
    public function export(Request $request)
    {
        $status = $request->get('status');
        
        $query = JobAssignment::with([
            'technician:id,first_name,last_name', 
            'region:id,name', 
            'client:id,company_name'
        ]);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $assignments = $query->orderBy('scheduled_date', 'desc')->get();
        
        $filename = 'job-assignments-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Assignment ID', 'Technician', 'Region', 'Terminals Count', 
                'Client', 'Scheduled Date', 'Service Type', 'Priority', 
                'Status', 'Estimated Hours', 'Notes'
            ]);
            
            foreach ($assignments as $assignment) {
                $technicianName = '';
                if ($assignment->technician) {
                    $technicianName = $assignment->technician->first_name . ' ' . $assignment->technician->last_name;
                }
                
                // Get service type name from category
                $serviceTypeCategory = Category::findBySlugAndType($assignment->service_type, Category::TYPE_SERVICE_TYPE);
                $serviceTypeName = $serviceTypeCategory ? $serviceTypeCategory->name : $assignment->service_type;
                
                fputcsv($file, [
                    $assignment->assignment_id,
                    $technicianName,
                    $assignment->region->name ?? 'N/A',
                    count($assignment->pos_terminals),
                    $assignment->client->company_name ?? 'N/A',
                    $assignment->scheduled_date->format('Y-m-d'),
                    $serviceTypeName,
                    $assignment->priority,
                    $assignment->status,
                    $assignment->estimated_duration_hours,
                    $assignment->notes
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}