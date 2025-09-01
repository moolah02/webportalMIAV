<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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


     public function getRegionTerminals($regionId, Request $request)
    {
        try {
            // Optional: validate inputs
            $request->validate([
                'client_id' => 'nullable|integer',
            ]);

            \Log::info('Getting terminals for region', [
                'region_id' => (int) $regionId,
                'client_id' => $request->get('client_id')
            ]);

            $query = PosTerminal::query()
                ->where('region_id', (int) $regionId)
                ->where('is_active', true);

            if ($request->filled('client_id')) {
                $query->where('client_id', (int) $request->get('client_id'));
            }

            $terminals = $query
                ->with(['client:id,company_name']) // keep your existing shape
                ->select([
                    'id',
                    'terminal_id',
                    'merchant_name',
                    'client_id',
                    'status',
                    'address',
                    'physical_address',
                    'region_id',
                    'is_active',
                ])
                ->orderBy('terminal_id')
                ->get();

            \Log::info('Terminals found', [
                'count' => $terminals->count(),
                // avoid logging the whole payload in prod if large
            ]);

            return response()->json([
                'success'   => true,
                'count'     => $terminals->count(),
                'terminals' => $terminals,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error loading terminals', [
                'region_id' => $regionId,
                'error'     => $e->getMessage(),
            ]);

            return response()->json([
                'success'   => false,
                'message'   => 'Error loading terminals: '.$e->getMessage(),
                'terminals' => [],
            ], 500);
        }
    }

public function mine(Request $request)
{
    $me = $request->user();

    // Pull jobs (no filters, no pagination)
    $jobs = JobAssignment::query()
        ->with([
            'region:id,name',
            'client:id,company_name',
            'project:id,project_name',
        ])
        ->where('technician_id', (int) $me->id)
        ->orderByDesc('scheduled_date')
        ->get();

    // Collect ALL ids from job->pos_terminals
    $allIdsRaw = [];
    foreach ($jobs as $ja) {
        $ids = $this->parseTerminalIds($ja->pos_terminals);
        if (!empty($ids)) {
            $allIdsRaw = array_merge($allIdsRaw, $ids);
        }
    }
    // Unique AFTER normalization for consistency
    $allIdsNorm = array_values(array_unique(array_map(fn($v) => $this->key($v), $allIdsRaw)));

    // Also collect a numeric-only set to try matching by primary key `id`
    $numericIds = array_values(array_filter(array_map(function ($v) {
        $n = $this->normalizeTid($v);
        return ctype_digit($n) ? $n : null;
    }, $allIdsRaw)));

    // Fetch terminals once: try all three keys: terminal_id, merchant_id, id
    $terminalsByKey = [
        'tid' => collect(),
        'mid' => collect(),
        'id'  => collect(),
    ];

    if (!empty($allIdsRaw)) {
        // Use the *raw trimmed* values for DB whereIn (MySQL is usually case-insensitive for text)
        $trimmedRaw = array_values(array_unique(array_filter(array_map(fn($v) => $this->normalizeTid($v), $allIdsRaw))));

        $terminals = PosTerminal::query()
            ->select([
                'id',
                'terminal_id',
                'merchant_id',
                'merchant_name',
                'current_status',
                'physical_address',
                'client_id',
            ])
            ->where(function ($q) use ($trimmedRaw, $numericIds) {
                if (!empty($trimmedRaw)) {
                    $q->whereIn('terminal_id', $trimmedRaw)
                      ->orWhereIn('merchant_id', $trimmedRaw);
                }
                if (!empty($numericIds)) {
                    $q->orWhereIn('id', $numericIds);
                }
            })
            ->get();

        // Build maps with normalized, case-insensitive keys
        $terminalsByKey['tid'] = $terminals->filter(fn($t) => $t->terminal_id !== null)
            ->keyBy(fn($t) => $this->key($t->terminal_id));

        $terminalsByKey['mid'] = $terminals->filter(fn($t) => $t->merchant_id !== null)
            ->keyBy(fn($t) => $this->key($t->merchant_id));

        $terminalsByKey['id']  = $terminals->keyBy(fn($t) => $this->key((string) $t->id));
    }

    // Shape the response per job using ONLY the job’s pos_terminals list
    $data = $jobs->map(function ($ja) use ($terminalsByKey) {
        $idsRaw  = $this->parseTerminalIds($ja->pos_terminals);
        $idsNorm = array_map(fn($x) => $this->key($x), $idsRaw);

        // Resolve each id by terminal_id -> merchant_id -> numeric id
        $jobTerminals = collect($idsNorm)->map(function ($k) use ($terminalsByKey) {
            return $terminalsByKey['tid']->get($k)
                ?? $terminalsByKey['mid']->get($k)
                ?? $terminalsByKey['id']->get($k);
        })->filter()->values();

        // Deduplicate terminal records by their primary key to be safe
        $jobTerminals = $jobTerminals->unique('id')->values();

        $merchants = $jobTerminals
            ->pluck('merchant_name')
            ->map(fn($m) => trim((string) $m))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return [
            // Core
            'id'             => $ja->id,
            'assignment_id'  => $ja->assignment_id,
            'status'         => $ja->status,
            'priority'       => $ja->priority,
            'scheduled_date' => optional($ja->scheduled_date)->toDateString(),
            'notes'          => $ja->notes,

            // Related
            'project_name'   => optional($ja->project)->project_name,
            'service_type'   => $ja->service_type,
            'client_name'    => optional($ja->client)->company_name,
            'region_name'    => optional($ja->region)->name,

            // Terminals
            'terminals_count' => count(array_unique($idsNorm)),
            'merchants'       => $merchants,
            'merchants_count' => $merchants->count(),

            'terminals'       => $jobTerminals->map(function ($t) {
                return [
                    'id'               => $t->id,
                    'terminal_id'      => $t->terminal_id,
                    'merchant_id'      => $t->merchant_id,
                    'merchant_name'    => $t->merchant_name,
                    'current_status'   => $t->current_status,
                    'physical_address' => $t->physical_address,
                    'client_id'        => $t->client_id,
                ];
            })->values(),

            // Timestamps
            'created_at'     => optional($ja->created_at)->toISOString(),
            'updated_at'     => optional($ja->updated_at)->toISOString(),
        ];
    });

    return response()->json([
        'success' => true,
        'scope'   => 'mine',
        'count'   => $data->count(),
        'data'    => $data,
    ]);
}

/**
 * Normalize a single terminal-like id consistently:
 * - trim spaces
 * - strip quotes/brackets/parentheses
 * - keep internal characters, but unify case for matching
 */
private function normalizeTid($v): string
{
    $v = trim((string) $v);
    $v = trim($v, " \t\n\r\0\x0B\"'[]()");
    // collapse weird unicode spaces
    $v = preg_replace('/\s+/u', ' ', $v ?? '');
    return $v;
}

/**
 * Case-insensitive key for lookups after normalization.
 */
private function key($v): string
{
    return mb_strtoupper($this->normalizeTid($v), 'UTF-8');
}

/**
 * Robustly parse terminal IDs from various shapes:
 * - array (preferred; add casts on model)
 * - JSON string '["T001","T002"]'
 * - CSV/pipe/space 'T001, T002 | 003' or 'T001 T002'
 * - null/empty -> []
 */
private function parseTerminalIds($raw): array
{
    $norm = fn($x) => $this->normalizeTid($x);

    if (is_array($raw)) {
        return array_values(array_filter(array_map($norm, $raw), fn($v) => $v !== ''));
    }

    if (is_string($raw)) {
        $trimmed = trim($raw);

        // JSON array?
        if (Str::startsWith($trimmed, '[') && Str::endsWith($trimmed, ']')) {
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_filter(array_map($norm, $decoded), fn($v) => $v !== ''));
            }
        }

        // Fallback: split on comma / pipe / whitespace
        $parts = preg_split('/[,\|\s]+/u', $trimmed);
        return array_values(array_filter(array_map($norm, $parts), fn($v) => $v !== ''));
    }

    return [];
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
