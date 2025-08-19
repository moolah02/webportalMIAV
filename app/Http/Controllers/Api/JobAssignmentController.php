<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\JobAssignment;
use App\Models\PosTerminal;
use Carbon\Carbon;

class JobAssignmentController extends Controller
{
    /**
     * Get all assignments (with filtering)
     */
    public function index(Request $request)
    {
        $query = JobAssignment::with(['technician', 'posTerminals', 'client']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        if ($request->has('region')) {
            $query->whereHas('posTerminals', function($q) use ($request) {
                $q->where('region', $request->region);
            });
        }

        $assignments = $query->orderBy('scheduled_date', 'desc')
                           ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $assignments->items(),
                'pagination' => [
                    'current_page' => $assignments->currentPage(),
                    'last_page' => $assignments->lastPage(),
                    'per_page' => $assignments->perPage(),
                    'total' => $assignments->total(),
                ]
            ]
        ]);
    }

    /**
     * Get assignments for current authenticated user (technician)
     */
    public function myAssignments(Request $request)
    {
        $employee = $request->user();
        
        $query = JobAssignment::with(['posTerminals', 'client'])
                              ->where('technician_id', $employee->id);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Default to upcoming assignments if no filters
        if (!$request->has('status') && !$request->has('date_from')) {
            $query->whereIn('status', ['pending', 'in_progress'])
                  ->whereDate('scheduled_date', '>=', Carbon::today());
        }

        $assignments = $query->orderBy('scheduled_date', 'asc')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $assignments->map(function($assignment) {
                    return [
                        'id' => $assignment->id,
                        'title' => $assignment->title,
                        'description' => $assignment->description,
                        'status' => $assignment->status,
                        'priority' => $assignment->priority,
                        'scheduled_date' => $assignment->scheduled_date,
                        'estimated_duration' => $assignment->estimated_duration,
                        'actual_start_time' => $assignment->actual_start_time,
                        'actual_end_time' => $assignment->actual_end_time,
                        'client' => [
                            'id' => $assignment->client->id,
                            'name' => $assignment->client->name,
                            'contact_person' => $assignment->client->contact_person,
                            'phone' => $assignment->client->phone,
                        ],
                        'terminals' => $assignment->posTerminals->map(function($terminal) {
                            return [
                                'id' => $terminal->id,
                                'terminal_id' => $terminal->terminal_id,
                                'location' => $terminal->location,
                                'address' => $terminal->address,
                                'status' => $terminal->status,
                                'coordinates' => [
                                    'latitude' => $terminal->latitude,
                                    'longitude' => $terminal->longitude,
                                ]
                            ];
                        }),
                        'notes' => $assignment->notes,
                        'photos' => $assignment->photos ?? [],
                        'created_at' => $assignment->created_at,
                        'updated_at' => $assignment->updated_at,
                    ];
                })
            ]
        ]);
    }

    /**
     * Show specific assignment
     */
    public function show(Request $request, $id)
    {
        $assignment = JobAssignment::with(['technician', 'posTerminals', 'client'])
                                  ->findOrFail($id);

        // Check if user can view this assignment
        $employee = $request->user();
        if (!$employee->hasPermission('manage_team') && 
            !$employee->hasPermission('all') && 
            $assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this assignment'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'assignment' => [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'status' => $assignment->status,
                    'priority' => $assignment->priority,
                    'scheduled_date' => $assignment->scheduled_date,
                    'estimated_duration' => $assignment->estimated_duration,
                    'actual_start_time' => $assignment->actual_start_time,
                    'actual_end_time' => $assignment->actual_end_time,
                    'technician' => [
                        'id' => $assignment->technician->id,
                        'name' => $assignment->technician->name,
                        'phone' => $assignment->technician->phone,
                    ],
                    'client' => [
                        'id' => $assignment->client->id,
                        'name' => $assignment->client->name,
                        'contact_person' => $assignment->client->contact_person,
                        'phone' => $assignment->client->phone,
                        'email' => $assignment->client->email,
                        'address' => $assignment->client->address,
                    ],
                    'terminals' => $assignment->posTerminals,
                    'notes' => $assignment->notes,
                    'photos' => $assignment->photos ?? [],
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                ]
            ]
        ]);
    }

    /**
     * Update assignment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'notes' => 'sometimes|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        // Check permissions
        if (!$employee->hasPermission('manage_team') && 
            !$employee->hasPermission('all') && 
            $assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this assignment'
            ], 403);
        }

        $assignment->status = $request->status;
        
        if ($request->has('notes')) {
            $assignment->notes = $request->notes;
        }

        // Set timestamps based on status
        if ($request->status === 'in_progress' && !$assignment->actual_start_time) {
            $assignment->actual_start_time = now();
        }

        if ($request->status === 'completed' && !$assignment->actual_end_time) {
            $assignment->actual_end_time = now();
        }

        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Assignment status updated successfully',
            'data' => [
                'assignment' => $assignment
            ]
        ]);
    }

    /**
     * Start a job
     */
    public function startJob(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'notes' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        // Check if this is the assigned technician
        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this job'
            ], 403);
        }

        if ($assignment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This job has already been started or completed'
            ], 422);
        }

        $assignment->status = 'in_progress';
        $assignment->actual_start_time = now();
        
        if ($request->has('latitude') && $request->has('longitude')) {
            $assignment->start_location = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'timestamp' => now()->toISOString()
            ];
        }

        if ($request->has('notes')) {
            $assignment->notes = $assignment->notes . "\n\nStart Notes: " . $request->notes;
        }

        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Job started successfully',
            'data' => [
                'assignment' => $assignment
            ]
        ]);
    }

    /**
     * Complete a job
     */
    public function completeJob(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'completion_notes' => 'required|string|max:1000',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'photos' => 'sometimes|array',
            'photos.*' => 'image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        // Check if this is the assigned technician
        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this job'
            ], 403);
        }

        if ($assignment->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'This job must be in progress to complete it'
            ], 422);
        }

        $assignment->status = 'completed';
        $assignment->actual_end_time = now();
        $assignment->completion_notes = $request->completion_notes;
        
        if ($request->has('latitude') && $request->has('longitude')) {
            $assignment->end_location = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'timestamp' => now()->toISOString()
            ];
        }

        // Handle photo uploads
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('job-photos', 'public');
                $photos[] = [
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                    'uploaded_at' => now()->toISOString()
                ];
            }
            $assignment->photos = array_merge($assignment->photos ?? [], $photos);
        }

        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Job completed successfully',
            'data' => [
                'assignment' => $assignment
            ]
        ]);
    }

    /**
     * Add note to assignment
     */
    public function addNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        // Check permissions
        if (!$employee->hasPermission('manage_team') && 
            !$employee->hasPermission('all') && 
            $assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add notes to this assignment'
            ], 403);
        }

        $noteEntry = "\n\n[" . now()->format('Y-m-d H:i:s') . "] " . $employee->name . ": " . $request->note;
        $assignment->notes = $assignment->notes . $noteEntry;
        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully'
        ]);
    }

    /**
     * Upload photos to assignment
     */
    public function uploadPhoto(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:2048',
            'description' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        // Check permissions
        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to add photos to this assignment'
            ], 403);
        }

        $path = $request->file('photo')->store('job-photos', 'public');
        
        $photoData = [
            'path' => $path,
            'url' => asset('storage/' . $path),
            'description' => $request->description ?? '',
            'uploaded_by' => $employee->name,
            'uploaded_at' => now()->toISOString()
        ];

        $photos = $assignment->photos ?? [];
        $photos[] = $photoData;
        $assignment->photos = $photos;
        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'data' => [
                'photo' => $photoData
            ]
        ]);
    }

    /**
     * Get photos for assignment
     */
    public function getPhotos($id)
    {
        $assignment = JobAssignment::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'photos' => $assignment->photos ?? []
            ]
        ]);
    }

    /**
     * Update location during assignment
     */
    public function updateLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $locationData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => now()->toISOString()
        ];

        $assignment->current_location = $locationData;
        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }

    /**
     * Check in to assignment location
     */
    public function checkIn(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $checkInData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => now()->toISOString(),
            'notes' => $request->notes ?? ''
        ];

        $assignment->check_in_data = $checkInData;
        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully',
            'data' => [
                'check_in' => $checkInData
            ]
        ]);
    }

    /**
     * Check out from assignment location
     */
    public function checkOut(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'notes' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment = JobAssignment::findOrFail($id);
        $employee = $request->user();

        if ($assignment->technician_id !== $employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $checkOutData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timestamp' => now()->toISOString(),
            'notes' => $request->notes ?? ''
        ];

        $assignment->check_out_data = $checkOutData;
        $assignment->save();

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'data' => [
                'check_out' => $checkOutData
            ]
        ]);
    }

    /**
     * Sync assignments for offline use
     */
    public function syncAssignments(Request $request)
    {
        $employee = $request->user();
        
        $assignments = JobAssignment::with(['posTerminals', 'client'])
                                  ->where('technician_id', $employee->id)
                                  ->whereIn('status', ['pending', 'in_progress'])
                                  ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'assignments' => $assignments,
                'sync_timestamp' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Bulk update for offline sync
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.assignment_id' => 'required|exists:job_assignments,id',
            'updates.*.data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = $request->user();
        $results = [];

        foreach ($request->updates as $update) {
            $assignment = JobAssignment::find($update['assignment_id']);
            
            if ($assignment && $assignment->technician_id === $employee->id) {
                $assignment->update($update['data']);
                $results[] = [
                    'assignment_id' => $assignment->id,
                    'status' => 'updated'
                ];
            } else {
                $results[] = [
                    'assignment_id' => $update['assignment_id'],
                    'status' => 'unauthorized'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'results' => $results
            ]
        ]);
    }
}