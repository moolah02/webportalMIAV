<?php
// ============================================
// TECHNICIAN API CONTROLLER
// ============================================

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\JobAssignment;

class TechnicianController extends Controller
{
    /**
     * Get list of technicians
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Check permissions
        if (!$user->hasPermission('manage_team') && !$user->hasPermission('all')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view technicians'
            ], 403);
        }

        $technicians = Employee::fieldTechnicians()
                              ->active()
                              ->with('role:id,name')
                              ->withCount([
                                  'jobAssignments as active_jobs' => function($query) {
                                      $query->whereIn('status', ['assigned', 'in_progress']);
                                  },
                                  'jobAssignments as completed_jobs' => function($query) {
                                      $query->where('status', 'completed');
                                  }
                              ])
                              ->get();

        $technicianData = $technicians->map(function($technician) {
            return [
                'id' => $technician->id,
                'name' => $technician->first_name . ' ' . $technician->last_name,
                'employee_number' => $technician->employee_number,
                'phone' => $technician->phone,
                'email' => $technician->email,
                'role' => $technician->role->name ?? 'Technician',
                'department' => $technician->department->name ?? 'Field Service',
                'active_jobs' => $technician->active_jobs,
                'completed_jobs' => $technician->completed_jobs,
                'specialization' => $technician->getSpecialization(),
                'status' => $technician->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'technicians' => $technicianData,
                'summary' => [
                    'total_technicians' => $technicianData->count(),
                    'active_technicians' => $technicianData->where('status', 'active')->count(),
                    'total_active_jobs' => $technicianData->sum('active_jobs'),
                ]
            ]
        ]);
    }

    /**
     * Get specific technician details
     */
    public function show($id)
    {
        $technician = Employee::fieldTechnicians()
                             ->with(['role:id,name', 'department:id,name'])
                             ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'technician' => [
                    'id' => $technician->id,
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'employee_number' => $technician->employee_number,
                    'phone' => $technician->phone,
                    'email' => $technician->email,
                    'role' => $technician->role->name ?? 'Technician',
                    'department' => $technician->department->name ?? 'Field Service',
                    'hire_date' => $technician->hire_date,
                    'status' => $technician->status,
                    'specialization' => $technician->getSpecialization(),
                ]
            ]
        ]);
    }

    /**
     * Get technician's assignments
     */
    public function getAssignments($id)
    {
        $technician = Employee::findOrFail($id);

        $assignments = JobAssignment::where('technician_id', $id)
                                   ->with(['client:id,company_name', 'region:id,name'])
                                   ->latest()
                                   ->get();

        $assignmentData = $assignments->map(function($assignment) {
            return [
                'id' => $assignment->id,
                'assignment_id' => $assignment->assignment_id,
                'client_name' => $assignment->client->company_name ?? 'Unknown',
                'region' => $assignment->region->name ?? $assignment->region,
                'scheduled_date' => $assignment->scheduled_date,
                'status' => $assignment->status,
                'priority' => $assignment->priority,
                'service_type' => $assignment->service_type,
                'terminal_count' => count($assignment->pos_terminals ?? []),
                'estimated_duration' => $assignment->estimated_duration_hours,
                'actual_start_time' => $assignment->actual_start_time,
                'actual_end_time' => $assignment->actual_end_time,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'technician' => [
                    'id' => $technician->id,
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                ],
                'assignments' => $assignmentData,
                'summary' => [
                    'total' => $assignmentData->count(),
                    'pending' => $assignmentData->where('status', 'assigned')->count(),
                    'in_progress' => $assignmentData->where('status', 'in_progress')->count(),
                    'completed' => $assignmentData->where('status', 'completed')->count(),
                ]
            ]
        ]);
    }

    /**
     * Get technician's current location (if available)
     */
    public function getCurrentLocation($id)
    {
        $technician = Employee::findOrFail($id);

        // Get current location from active job assignment
        $activeAssignment = JobAssignment::where('technician_id', $id)
                                        ->where('status', 'in_progress')
                                        ->first();

        $location = null;
        if ($activeAssignment && isset($activeAssignment->current_location)) {
            $location = $activeAssignment->current_location;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'technician_id' => $id,
                'current_location' => $location,
                'last_updated' => $location ? $location['timestamp'] ?? null : null,
                'is_active' => $activeAssignment ? true : false,
            ]
        ]);
    }

    /**
     * Update technician availability
     */
    public function updateAvailability(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'available' => 'required|boolean',
            'reason' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $technician = Employee::findOrFail($id);
        $user = $request->user();

        // Check permissions
        if ($user->id !== $id && !$user->hasPermission('manage_team') && !$user->hasPermission('all')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update availability'
            ], 403);
        }

        try {
            // Update availability (you might want to add availability fields to Employee model)
            $availabilityData = [
                'available' => $request->available,
                'reason' => $request->reason,
                'updated_at' => now()->toISOString(),
                'updated_by' => $user->id,
            ];

            // Log availability change for now
            \Log::info("Technician {$technician->employee_number} availability updated", $availabilityData);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully',
                'data' => [
                    'technician_id' => $id,
                    'available' => $request->available,
                    'updated_at' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Availability update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability'
            ], 500);
        }
    }
}