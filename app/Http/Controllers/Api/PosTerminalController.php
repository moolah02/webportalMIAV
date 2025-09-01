<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Validation\Rule;


class PosTerminalController extends Controller
{


    /**
 * Authorization helper shared by update endpoints.
 */
protected function canUpdate($user): bool
{
    return $user && (
        $user->hasPermission('update_terminals') ||
        $user->hasPermission('manage_team') ||
        $user->hasPermission('all')
    );
}

/**
 * Common validation rules for PUT/PATCH updates.
 * $requireAll = true => strict PUT (everything must be present)
 */
protected function updateRules(bool $requireAll = false): array
{
    $sometimes = $requireAll ? [] : ['sometimes'];
    $nullable  = $requireAll ? [] : ['nullable'];

    return [
        'merchant_id'             => array_merge($sometimes, $nullable, ['string','max:191']),
        'terminal_id'             => array_merge($sometimes, $nullable, ['string','max:191']),
        'client_id'               => array_merge($sometimes, $nullable, ['integer']),
        'merchant_name'           => array_merge($sometimes, $nullable, ['string','max:255']),
        'legal_name'              => array_merge($sometimes, $nullable, ['string','max:255']),
        'merchant_contact_person' => array_merge($sometimes, $nullable, ['string','max:255']),
        'merchant_phone'          => array_merge($sometimes, $nullable, ['string','max:50']),
        'merchant_email'          => array_merge($sometimes, $nullable, ['email','max:255']),
        'physical_address'        => array_merge($sometimes, $nullable, ['string','max:500']),
        'city'                    => array_merge($sometimes, $nullable, ['string','max:191']),
        'province'                => array_merge($sometimes, $nullable, ['string','max:191']),
        'area'                    => array_merge($sometimes, $nullable, ['string','max:191']),
        'business_type'           => array_merge($sometimes, $nullable, ['string','max:191']),
        'installation_date'       => array_merge($sometimes, $nullable, ['date']),
        'terminal_model'          => array_merge($sometimes, $nullable, ['string','max:191']),
        'serial_number'           => array_merge($sometimes, $nullable, ['string','max:191']),
        'contract_details'        => array_merge($sometimes, $nullable, ['string']),
        'status'                  => array_merge($sometimes, $nullable, ['string','max:100']),
        'last_service_date'       => array_merge($sometimes, $nullable, ['date']),
        'next_service_due'        => array_merge($sometimes, $nullable, ['date']),
        'region_id'               => array_merge($sometimes, $nullable, ['integer']),
        'region'                  => array_merge($sometimes, $nullable, ['string','max:191']),
        'current_status'          => array_merge($sometimes, $nullable, ['string','max:100']),
        'deployment_status'       => array_merge($sometimes, $nullable, ['string','max:100']),
        'condition_status'        => array_merge($sometimes, $nullable, ['string','max:100']),
        'issues_raised'           => array_merge($sometimes, $nullable, ['string']),
        'corrective_action'       => array_merge($sometimes, $nullable, ['string']),
        'site_contact_person'     => array_merge($sometimes, $nullable, ['string','max:255']),
        'site_contact_number'     => array_merge($sometimes, $nullable, ['string','max:50']),
        'last_updated_by'         => array_merge($sometimes, $nullable, ['string','max:191']),
        'last_visit_date'         => array_merge($sometimes, $nullable, ['date']),
        'extra_fields'            => array_merge($sometimes, $nullable, ['array']),

        // JSON blobs
        'status_info' => ['sometimes','array'],
        'coordinates' => ['sometimes','array'],

        // nested props
        'status_info.is_active'     => ['sometimes','boolean'],
        'status_info.is_faulty'     => ['sometimes','boolean'],
        'status_info.needs_service' => ['sometimes','boolean'],

        'coordinates.latitude'  => ['sometimes','nullable','numeric','between:-90,90'],
        'coordinates.longitude' => ['sometimes','nullable','numeric','between:-180,180'],
    ];
}

/**
 * PUT/PATCH /api/pos-terminals/id/{id}
 * Update by numeric DB id.
 */
public function updateById(Request $request, int $id)
{
    $user = $request->user();
    if (!$this->canUpdate($user)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $terminal = PosTerminal::query()->whereKey($id)->firstOrFail();
    $data = $request->validate($this->updateRules(false)); // PATCH-friendly

    $terminal->fill($data);

    if (!$request->filled('last_updated_by')) {
        $terminal->last_updated_by = $user->id ?? null;
    }

    $terminal->save();

    return response()->json([
        'success' => true,
        'message' => 'Terminal updated successfully.',
        'data'    => $terminal->fresh(),
    ]);
}

/**
 * PUT/PATCH /api/pos-terminals/{terminalId}
 * Update by business key terminal_id (string).
 */
public function updateByTerminalId(Request $request, string $terminalId)
{
    $user = $request->user();
    if (!$this->canUpdate($user)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $terminal = PosTerminal::query()
        ->where('terminal_id', $terminalId)
        ->firstOrFail();

    $data = $request->validate($this->updateRules(false));

    $terminal->fill($data);

    if (!$request->filled('last_updated_by')) {
        $terminal->last_updated_by = $user->id ?? null;
    }

    $terminal->save();

    return response()->json([
        'success' => true,
        'message' => 'Terminal updated successfully.',
        'data'    => $terminal->fresh(),
    ]);
}

/**
 * PATCH /api/pos-terminals/bulk
 * Body: [{ "terminal_id": "TERM-001", <fields...> }, ...]
 */
public function bulkUpdateByTerminalId(Request $request)
{
    $user = $request->user();
    if (!$this->canUpdate($user)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $rows = $request->all();
    if (!is_array($rows)) {
        return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
    }

    $rules = $this->updateRules(false);
    $updated = [];

    foreach ($rows as $i => $row) {
        // Validate each row
        $validator = validator($row, array_merge(['terminal_id' => ['required','string','max:191']], $rules));
        $validator->validate();

        $t = PosTerminal::query()->where('terminal_id', $row['terminal_id'])->first();
        if (!$t) {
            // Skip missing terminals; you can collect “not found” too if you prefer
            continue;
        }

        $data = $row;
        unset($data['terminal_id']); // avoid changing the key during bulk unless you intend to

        $t->fill($data);

        if (empty($row['last_updated_by'])) {
            $t->last_updated_by = $user->id ?? null;
        }

        $t->save();
        $updated[] = $t->fresh();
    }

    return response()->json([
        'success' => true,
        'count'   => count($updated),
        'data'    => $updated,
    ]);
}


    /**
     * Get paginated list of POS terminals with filtering
     */
    public function index(Request $request)
    {
        $query = PosTerminal::with(['client:id,company_name,contact_person,phone']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('terminal_id', 'like', "%{$search}%")
                  ->orWhere('merchant_name', 'like', "%{$search}%")
                  ->orWhere('merchant_contact_person', 'like', "%{$search}%")
                  ->orWhere('physical_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Apply technician-specific filtering (if user is a technician)
        $user = $request->user();
        if ($user && $user->isFieldTechnician() && $request->get('my_assignments_only', false)) {
            $query->whereHas('jobAssignments', function($q) use ($user) {
                $q->where('technician_id', $user->id)
                  ->whereIn('status', ['assigned', 'in_progress']);
            });
        }

        $terminals = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

        // Transform the data for API response
        $terminals->getCollection()->transform(function ($terminal) {
            return [
                'id' => $terminal->id,
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'merchant_contact_person' => $terminal->merchant_contact_person,
                'merchant_phone' => $terminal->merchant_phone,
                'merchant_email' => $terminal->merchant_email,
                'physical_address' => $terminal->physical_address,
                'region' => $terminal->region,
                'city' => $terminal->city,
                'province' => $terminal->province,
                'business_type' => $terminal->business_type,
                'terminal_model' => $terminal->terminal_model,
                'serial_number' => $terminal->serial_number,
                'status' => $terminal->status,
                'current_status' => $terminal->current_status,
                'installation_date' => $terminal->installation_date,
                'last_service_date' => $terminal->last_service_date,
                'next_service_due' => $terminal->next_service_due,
                'client' => $terminal->client ? [
                    'id' => $terminal->client->id,
                    'name' => $terminal->client->company_name,
                    'contact_person' => $terminal->client->contact_person,
                    'phone' => $terminal->client->phone,
                ] : null,
                'status_info' => [
                    'is_active' => $terminal->isActive(),
                    'is_faulty' => $terminal->isFaulty(),
                    'needs_service' => $terminal->needsService(),
                ],
                'coordinates' => [
                    'latitude' => $terminal->latitude ?? null,
                    'longitude' => $terminal->longitude ?? null,
                ],
                'created_at' => $terminal->created_at,
                'updated_at' => $terminal->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'terminals' => $terminals->items(),
                'pagination' => [
                    'current_page' => $terminals->currentPage(),
                    'last_page' => $terminals->lastPage(),
                    'per_page' => $terminals->perPage(),
                    'total' => $terminals->total(),
                    'from' => $terminals->firstItem(),
                    'to' => $terminals->lastItem(),
                ]
            ]
        ]);
    }

    /**
     * Get single terminal details
     */
    public function show(Request $request, $id)
    {
        $terminal = PosTerminal::with([
            'client:id,company_name,contact_person,phone,email,address'
        ])->findOrFail($id);

        // Get recent tickets for this terminal
        $recentTickets = [];
        try {
            $recentTickets = Ticket::where('pos_terminal_id', $terminal->id)
                                  ->with('technician:id,first_name,last_name')
                                  ->latest()
                                  ->limit(5)
                                  ->get()
                                  ->map(function($ticket) {
                                      return [
                                          'id' => $ticket->id,
                                          'ticket_id' => $ticket->ticket_id,
                                          'priority' => $ticket->priority,
                                          'status' => $ticket->status,
                                          'issue_type' => $ticket->issue_type,
                                          'title' => $ticket->title,
                                          'created_at' => $ticket->created_at,
                                          'technician' => $ticket->technician ? [
                                              'name' => $ticket->technician->first_name . ' ' . $ticket->technician->last_name
                                          ] : null,
                                      ];
                                  });
        } catch (\Exception $e) {
            \Log::warning('Could not load tickets: ' . $e->getMessage());
        }

        // Get recent job assignments
        $recentJobs = [];
        try {
            if (method_exists($terminal, 'jobAssignments')) {
                $recentJobs = $terminal->jobAssignments()
                                      ->with('technician:id,first_name,last_name')
                                      ->latest()
                                      ->limit(5)
                                      ->get()
                                      ->map(function($job) {
                                          return [
                                              'id' => $job->id,
                                              'assignment_id' => $job->assignment_id,
                                              'status' => $job->status,
                                              'scheduled_date' => $job->scheduled_date,
                                              'service_type' => $job->service_type,
                                              'technician' => $job->technician ? [
                                                  'name' => $job->technician->first_name . ' ' . $job->technician->last_name
                                              ] : null,
                                          ];
                                      });
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load job assignments: ' . $e->getMessage());
        }

        $terminalData = [
            'id' => $terminal->id,
            'terminal_id' => $terminal->terminal_id,
            'merchant_name' => $terminal->merchant_name,
            'merchant_contact_person' => $terminal->merchant_contact_person,
            'merchant_phone' => $terminal->merchant_phone,
            'merchant_email' => $terminal->merchant_email,
            'physical_address' => $terminal->physical_address,
            'region' => $terminal->region,
            'city' => $terminal->city,
            'province' => $terminal->province,
            'area' => $terminal->area,
            'business_type' => $terminal->business_type,
            'terminal_model' => $terminal->terminal_model,
            'serial_number' => $terminal->serial_number,
            'status' => $terminal->status,
            'current_status' => $terminal->current_status,
            'installation_date' => $terminal->installation_date,
            'last_service_date' => $terminal->last_service_date,
            'next_service_due' => $terminal->next_service_due,
            'contract_details' => $terminal->contract_details,
            'client' => $terminal->client ? [
                'id' => $terminal->client->id,
                'name' => $terminal->client->company_name,
                'contact_person' => $terminal->client->contact_person,
                'phone' => $terminal->client->phone,
                'email' => $terminal->client->email,
                'address' => $terminal->client->address,
            ] : null,
            'status_info' => $terminal->status_info ?? [
                'is_active'=>null,'is_faulty'=>null,
                'needs_service'=>null
            ],
            'coordinates' => $terminal->coordinates ?? [
                'latitude'=>null,
                'longitude'=>null
            ],
            'recent_tickets' => $recentTickets,
            'recent_jobs' => $recentJobs,
            'created_at' => $terminal->created_at,
            'updated_at' => $terminal->updated_at,
        ];
        return response()->json([
            'success' => true,
            'data' => [
                'terminal' => $terminalData
            ]
        ]);
    }

    /**
     * Update terminal status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,offline,faulty,maintenance,decommissioned',
            'notes' => 'sometimes|string|max:500',
            'location' => 'sometimes|array',
            'location.latitude' => 'sometimes|numeric',
            'location.longitude' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $terminal = PosTerminal::findOrFail($id);
        $user = $request->user();

        // Check permissions
        if (!$user->hasPermission('update_terminals') &&
            !$user->hasPermission('manage_team') &&
            !$user->hasPermission('all')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update terminal status'
            ], 403);
        }

        try {
            $updateData = [
                'status' => $request->status,
                'current_status' => $request->status,
            ];

            // Add location if provided
            if ($request->has('location')) {
                $updateData['latitude'] = $request->location['latitude'] ?? null;
                $updateData['longitude'] = $request->location['longitude'] ?? null;
            }

            $terminal->update($updateData);

            // Log the status change if notes provided
            if ($request->has('notes')) {
                // You can add this to a status change log table if needed
                \Log::info("Terminal {$terminal->terminal_id} status changed to {$request->status} by {$user->name}. Notes: {$request->notes}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Terminal status updated successfully',
                'data' => [
                    'terminal_id' => $terminal->terminal_id,
                    'new_status' => $terminal->status,
                    'updated_at' => $terminal->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Terminal status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update terminal status'
            ], 500);
        }
    }

    /**
     * Create service report for terminal
     */
    public function createServiceReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'service_type' => 'required|string|in:maintenance,installation,repair,inspection',
            'description' => 'required|string|max:1000',
            'status_before' => 'required|string',
            'status_after' => 'required|string',
            'parts_used' => 'sometimes|array',
            'time_spent' => 'sometimes|integer|min:1',
            'photos' => 'sometimes|array',
            'photos.*' => 'image|max:2048',
            'location' => 'sometimes|array',
            'location.latitude' => 'sometimes|numeric',
            'location.longitude' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $terminal = PosTerminal::findOrFail($id);
        $user = $request->user();

        try {
            // Handle photo uploads
            $photos = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('service-reports', 'public');
                    $photos[] = [
                        'path' => $path,
                        'url' => asset('storage/' . $path),
                        'uploaded_at' => now()->toISOString()
                    ];
                }
            }

            // Create service report (you might need to create this model)
            $reportData = [
                'terminal_id' => $terminal->id,
                'technician_id' => $user->id,
                'service_type' => $request->service_type,
                'description' => $request->description,
                'status_before' => $request->status_before,
                'status_after' => $request->status_after,
                'parts_used' => $request->parts_used ?? [],
                'time_spent' => $request->time_spent,
                'photos' => $photos,
                'location_data' => $request->location ?? null,
                'service_date' => now(),
            ];

            // Update terminal status and last service date
            $terminal->update([
                'status' => $request->status_after,
                'current_status' => $request->status_after,
                'last_service_date' => now(),
            ]);

            // Log service report (you can save to database if you have a ServiceReport model)
            \Log::info('Service report created', $reportData);

            return response()->json([
                'success' => true,
                'message' => 'Service report created successfully',
                'data' => [
                    'report' => $reportData,
                    'terminal_updated' => true
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Service report creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service report'
            ], 500);
        }
    }

    /**
     * Get terminal service history
     */
    public function getHistory($id)
    {
        $terminal = PosTerminal::findOrFail($id);

        $history = collect();

        // Get job assignments history
        try {
            if (method_exists($terminal, 'jobAssignments')) {
                $jobs = $terminal->jobAssignments()
                                ->with('technician:id,first_name,last_name')
                                ->orderBy('created_at', 'desc')
                                ->get();

                foreach ($jobs as $job) {
                    $history->push([
                        'type' => 'job_assignment',
                        'id' => $job->id,
                        'title' => "Job Assignment: {$job->assignment_id}",
                        'description' => $job->service_type ?? 'Service',
                        'status' => $job->status,
                        'technician' => $job->technician ?
                            $job->technician->first_name . ' ' . $job->technician->last_name : null,
                        'date' => $job->scheduled_date,
                        'completed_at' => $job->actual_end_time,
                        'notes' => $job->notes,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load job assignments: ' . $e->getMessage());
        }

        // Get tickets history
        try {
            $tickets = Ticket::where('pos_terminal_id', $terminal->id)
                            ->with('technician:id,first_name,last_name')
                            ->orderBy('created_at', 'desc')
                            ->get();

            foreach ($tickets as $ticket) {
                $history->push([
                    'type' => 'ticket',
                    'id' => $ticket->id,
                    'title' => "Ticket: {$ticket->ticket_id}",
                    'description' => $ticket->title,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'technician' => $ticket->technician ?
                        $ticket->technician->first_name . ' ' . $ticket->technician->last_name : null,
                    'date' => $ticket->created_at,
                    'resolved_at' => $ticket->resolved_at,
                    'issue_type' => $ticket->issue_type,
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load tickets: ' . $e->getMessage());
        }

        // Sort by date descending
        $history = $history->sortByDesc('date')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'terminal_id' => $terminal->terminal_id,
                'history' => $history
            ]
        ]);
    }

    /**
     * Search terminals
     */
    public function search($query)
    {
        $terminals = PosTerminal::with('client:id,company_name')
                               ->where(function($q) use ($query) {
                                   $q->where('terminal_id', 'like', "%{$query}%")
                                     ->orWhere('merchant_name', 'like', "%{$query}%")
                                     ->orWhere('physical_address', 'like', "%{$query}%")
                                     ->orWhere('serial_number', 'like', "%{$query}%");
                               })
                               ->limit(20)
                               ->get();

        $results = $terminals->map(function($terminal) {
            return [
                'id' => $terminal->id,
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'address' => $terminal->physical_address,
                'status' => $terminal->status,
                'client_name' => $terminal->client->company_name ?? 'Unknown',
                'region' => $terminal->region,
                'city' => $terminal->city,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'query' => $query,
                'results' => $results,
                'count' => $results->count()
            ]
        ]);
    }

    /**
     * Get terminals by region
     */
    public function getByRegion($region)
    {
        $terminals = PosTerminal::with('client:id,company_name')
                               ->where('region', $region)
                               ->orderBy('merchant_name')
                               ->get();

        $terminalData = $terminals->map(function($terminal) {
            return [
                'id' => $terminal->id,
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'status' => $terminal->status,
                'client_name' => $terminal->client->company_name ?? 'Unknown',
                'address' => $terminal->physical_address,
                'coordinates' => [
                    'latitude' => $terminal->latitude,
                    'longitude' => $terminal->longitude,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'region' => $region,
                'terminals' => $terminalData,
                'count' => $terminalData->count(),
                'stats' => [
                    'active' => $terminals->where('status', 'active')->count(),
                    'offline' => $terminals->where('status', 'offline')->count(),
                    'faulty' => $terminals->whereIn('status', ['faulty', 'maintenance'])->count(),
                ]
            ]
        ]);
    }

    /**
     * Get terminals by client
     */
    public function getByClient($clientId)
    {
        $client = Client::findOrFail($clientId);

        $terminals = PosTerminal::where('client_id', $clientId)
                               ->orderBy('merchant_name')
                               ->get();

        $terminalData = $terminals->map(function($terminal) {
            return [
                'id' => $terminal->id,
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'status' => $terminal->status,
                'region' => $terminal->region,
                'city' => $terminal->city,
                'address' => $terminal->physical_address,
                'installation_date' => $terminal->installation_date,
                'last_service_date' => $terminal->last_service_date,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'id' => $client->id,
                    'name' => $client->company_name,
                ],
                'terminals' => $terminalData,
                'count' => $terminalData->count(),
                'stats' => [
                    'active' => $terminals->where('status', 'active')->count(),
                    'offline' => $terminals->where('status', 'offline')->count(),
                    'faulty' => $terminals->whereIn('status', ['faulty', 'maintenance'])->count(),
                ]
            ]
        ]);
    }

    /**
     * Sync terminals for offline use
     */
    public function syncTerminals(Request $request)
    {
        $user = $request->user();

        // Get terminals relevant to the user
        $query = PosTerminal::with('client:id,company_name');

        // If user is a technician, get only terminals assigned to them or in their regions
        if ($user->isFieldTechnician()) {
            $assignedTerminalIds = collect();

            try {
                // Get terminals from their job assignments
                $assignedTerminalIds = \App\Models\JobAssignment::where('technician_id', $user->id)
                                                               ->whereIn('status', ['assigned', 'in_progress'])
                                                               ->get()
                                                               ->pluck('pos_terminals')
                                                               ->flatten()
                                                               ->unique();
            } catch (\Exception $e) {
                \Log::warning('Could not load assigned terminals: ' . $e->getMessage());
            }

            if ($assignedTerminalIds->isNotEmpty()) {
                $query->whereIn('id', $assignedTerminalIds);
            } else {
                // Fallback: get terminals in regions they work
                $regions = \App\Models\JobAssignment::where('technician_id', $user->id)
                                                   ->distinct()
                                                   ->pluck('region')
                                                   ->filter();

                if ($regions->isNotEmpty()) {
                    $query->whereIn('region', $regions);
                }
            }
        }

        $terminals = $query->get()->map(function($terminal) {
            return [
                'id' => $terminal->id,
                'terminal_id' => $terminal->terminal_id,
                'merchant_name' => $terminal->merchant_name,
                'status' => $terminal->status,
                'region' => $terminal->region,
                'city' => $terminal->city,
                'address' => $terminal->physical_address,
                'client_name' => $terminal->client->company_name ?? 'Unknown',
                'coordinates' => [
                    'latitude' => $terminal->latitude,
                    'longitude' => $terminal->longitude,
                ],
                'updated_at' => $terminal->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'terminals' => $terminals,
                'sync_timestamp' => now()->toISOString(),
                'total_count' => $terminals->count()
            ]
        ]);
    }

/**
 * Get chart data for AJAX requests
 */
public function getChartData(Request $request)
{
    try {
        $query = PosTerminal::query();

        // Apply same filters as index method with column qualification
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pos_terminals.terminal_id', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_name', 'like', "%{$search}%")
                  ->orWhere('pos_terminals.merchant_contact_person', 'like', "%{$search}%");
            });
        }

        if ($request->filled('client')) {
            $query->where('pos_terminals.client_id', $request->client);
        }

        if ($request->filled('status')) {
            $validStatuses = ['active', 'offline', 'faulty', 'maintenance'];
            if (in_array($request->status, $validStatuses)) {
                $query->where('pos_terminals.status', $request->status);
            }
        }

        if ($request->filled('region')) {
            $query->where('pos_terminals.region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('pos_terminals.city', $request->city);
        }

        if ($request->filled('province')) {
            $query->where('pos_terminals.province', $request->province);
        }

        $stats = $this->calculateFilteredStats(clone $query);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chartData' => [
                'stats' => $stats,
                'serviceDue' => [
                    'recentlyServiced' => $stats['recently_serviced'],
                    'serviceDueSoon' => max(0, $stats['service_due'] - $stats['overdue_service']),
                    'overdueService' => $stats['overdue_service'],
                    'neverServiced' => $stats['never_serviced']
                ],
                'clientDistribution' => $stats['client_distribution'],
                'modelDistribution' => $stats['model_distribution']
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Chart data error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading chart data: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get comprehensive statistics
 */
public function getComprehensiveStats(Request $request)
{
    try {
        $query = PosTerminal::query();
        $this->applyFilters($query, $request);

        $stats = $this->calculateComprehensiveStats(clone $query);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading statistics'
        ], 500);
    }
}

/**
 * Get service timeline specific data
 */
public function getServiceTimelineData(Request $request)
{
    try {
        $query = PosTerminal::query();
        $this->applyFilters($query, $request);

        $data = [
            'recently_serviced' => (clone $query)
                ->whereNotNull('last_service_date')
                ->where('last_service_date', '>=', now()->subDays(30))
                ->count(),

            'service_due_soon' => (clone $query)
                ->where(function($q) {
                    $q->whereNull('last_service_date')
                      ->orWhereBetween('last_service_date', [now()->subDays(90), now()->subDays(60)]);
                })
                ->count(),

            'overdue_service' => (clone $query)
                ->where(function($q) {
                    $q->whereNull('last_service_date')
                      ->orWhere('last_service_date', '<=', now()->subDays(90));
                })
                ->count(),

            'never_serviced' => (clone $query)
                ->whereNull('last_service_date')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading service timeline data'
        ], 500);
    }
}

/**
 * Get distribution data for charts
 */
public function getDistributionData(Request $request)
{
    try {
        $query = PosTerminal::query();
        $this->applyFilters($query, $request);

        $data = [
            'clients' => (clone $query)
                ->join('clients', 'pos_terminals.client_id', '=', 'clients.id')
                ->selectRaw('clients.company_name, COUNT(*) as count')
                ->groupBy('clients.id', 'clients.company_name')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'company_name'),

            'regions' => (clone $query)
                ->selectRaw('COALESCE(region, "Unknown") as region, COUNT(*) as count')
                ->groupBy('region')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'region'),

            'models' => (clone $query)
                ->selectRaw('COALESCE(terminal_model, "Unknown") as model, COUNT(*) as count')
                ->groupBy('terminal_model')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'model'),

            'business_types' => (clone $query)
                ->selectRaw('COALESCE(business_type, "Unknown") as type, COUNT(*) as count')
                ->groupBy('business_type')
                ->orderByDesc('count')
                ->limit(10)
                ->pluck('count', 'type')
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading distribution data'
        ], 500);
    }
}

/**
 * Get filtered statistics based on request parameters
 */
public function getFilteredStatistics(Request $request)
{
    try {
        $query = PosTerminal::query();

        // Apply filters from request body
        if ($request->has('filters')) {
            $filters = $request->input('filters');

            if (isset($filters['client_id'])) {
                $query->where('client_id', $filters['client_id']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['region'])) {
                $query->where('region', $filters['region']);
            }

            if (isset($filters['city'])) {
                $query->where('city', $filters['city']);
            }

            if (isset($filters['date_range'])) {
                $range = $filters['date_range'];
                if (isset($range['start']) && isset($range['end'])) {
                    $query->whereBetween('created_at', [$range['start'], $range['end']]);
                }
            }

            if (isset($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('terminal_id', 'like', "%{$search}%")
                      ->orWhere('merchant_name', 'like', "%{$search}%")
                      ->orWhere('merchant_contact_person', 'like', "%{$search}%");
                });
            }
        }

        $stats = $this->calculateComprehensiveStats(clone $query);

        return response()->json([
            'success' => true,
            'data' => $stats,
            'applied_filters' => $request->input('filters', [])
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading filtered statistics'
        ], 500);
    }
}

/**
 * Apply filters to query from request
 */
private function applyFilters($query, Request $request)
{
    if ($request->filled('client')) {
        $query->where('client_id', $request->client);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('region')) {
        $query->where('region', $request->region);
    }

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('terminal_id', 'like', "%{$search}%")
              ->orWhere('merchant_name', 'like', "%{$search}%")
              ->orWhere('merchant_contact_person', 'like', "%{$search}%");
        });
    }
}


private function calculateFilteredStats($baseQuery)
{
    // Basic status counts - FIX: Qualify ALL column names
    $totalTerminals = $baseQuery->count();
    $activeTerminals = (clone $baseQuery)->where('pos_terminals.status', 'active')->count();
    $offlineTerminals = (clone $baseQuery)->where('pos_terminals.status', 'offline')->count();
    $faultyTerminals = (clone $baseQuery)->whereIn('pos_terminals.status', ['faulty', 'maintenance'])->count();

    // Service-related statistics
    $recentlyServiced = (clone $baseQuery)
        ->whereNotNull('pos_terminals.last_service_date')
        ->where('pos_terminals.last_service_date', '>=', now()->subDays(30))
        ->count();

    $serviceDue = (clone $baseQuery)
        ->where(function($query) {
            $query->whereNull('pos_terminals.last_service_date')
                  ->orWhere('pos_terminals.last_service_date', '<=', now()->subDays(60));
        })
        ->count();

    // More granular service data
    $overdueService = (clone $baseQuery)
        ->where(function($query) {
            $query->whereNull('pos_terminals.last_service_date')
                  ->orWhere('pos_terminals.last_service_date', '<=', now()->subDays(90));
        })
        ->count();

    $neverServiced = (clone $baseQuery)
        ->whereNull('pos_terminals.last_service_date')
        ->count();

    // Installation statistics
    $recentInstallations = (clone $baseQuery)
        ->whereNotNull('pos_terminals.installation_date')
        ->where('pos_terminals.installation_date', '>=', now()->subDays(30))
        ->count();

    // Model distribution for alternative chart - FIX: Column qualification
    $modelDistribution = (clone $baseQuery)
        ->selectRaw('COALESCE(pos_terminals.terminal_model, "Unknown") as model, COUNT(*) as count')
        ->groupBy('pos_terminals.terminal_model')
        ->orderByDesc('count')
        ->limit(6)
        ->pluck('count', 'model')
        ->toArray();

    // Client distribution for alternative chart - FIX: No ambiguous columns
    $clientDistribution = [];
    try {
        $clientDistribution = (clone $baseQuery)
            ->join('clients', 'pos_terminals.client_id', '=', 'clients.id')
            ->selectRaw('clients.company_name, COUNT(pos_terminals.id) as count')
            ->groupBy('clients.id', 'clients.company_name')
            ->orderByDesc('count')
            ->limit(7)
            ->pluck('count', 'company_name')
            ->toArray();
    } catch (\Exception $e) {
        Log::warning('Error calculating client distribution: ' . $e->getMessage());
        // Fallback to simple client count
        $clientDistribution = ['Client Data' => $totalTerminals];
    }

    return [
        // Basic stats (for the 4 cards)
        'total_terminals' => $totalTerminals,
        'active_terminals' => $activeTerminals,
        'offline_terminals' => $offlineTerminals,
        'faulty_terminals' => $faultyTerminals,

        // Service timeline stats (for the new chart)
        'recently_serviced' => $recentlyServiced,
        'service_due' => $serviceDue,
        'overdue_service' => $overdueService,
        'never_serviced' => $neverServiced,

        // Additional useful stats
        'recent_installations' => $recentInstallations,
        'uptime_percentage' => $totalTerminals > 0 ? round(($activeTerminals / $totalTerminals) * 100, 1) : 0,

        // Chart data arrays
        'model_distribution' => $modelDistribution,
        'client_distribution' => $clientDistribution,
    ];
}

private function calculateComprehensiveStats($baseQuery)
{
    // Basic status counts
    $totalTerminals = $baseQuery->count();
    $activeTerminals = (clone $baseQuery)->where('status', 'active')->count();
    $offlineTerminals = (clone $baseQuery)->where('status', 'offline')->count();
    $faultyTerminals = (clone $baseQuery)->whereIn('status', ['faulty', 'maintenance'])->count();

    // Service-related statistics
    $recentlyServiced = (clone $baseQuery)
        ->whereNotNull('last_service_date')
        ->where('last_service_date', '>=', now()->subDays(30))
        ->count();

    $serviceDue = (clone $baseQuery)
        ->where(function($query) {
            $query->whereNull('last_service_date')
                  ->orWhere('last_service_date', '<=', now()->subDays(60));
        })
        ->count();

    $overdueService = (clone $baseQuery)
        ->where(function($query) {
            $query->whereNull('last_service_date')
                  ->orWhere('last_service_date', '<=', now()->subDays(90));
        })
        ->count();

    $neverServiced = (clone $baseQuery)
        ->whereNull('last_service_date')
        ->count();

    // Installation statistics
    $recentInstallations = (clone $baseQuery)
        ->whereNotNull('installation_date')
        ->where('installation_date', '>=', now()->subDays(30))
        ->count();

    // Model distribution for charts
    $modelDistribution = (clone $baseQuery)
        ->selectRaw('COALESCE(terminal_model, "Unknown") as model, COUNT(*) as count')
        ->groupBy('terminal_model')
        ->orderByDesc('count')
        ->limit(8)
        ->pluck('count', 'model')
        ->toArray();

    // Client distribution for charts
    $clientDistribution = (clone $baseQuery)
        ->join('clients', 'pos_terminals.client_id', '=', 'clients.id')
        ->selectRaw('clients.company_name, COUNT(*) as count')
        ->groupBy('clients.id', 'clients.company_name')
        ->orderByDesc('count')
        ->limit(7)
        ->pluck('count', 'company_name')
        ->toArray();

    // Regional distribution for enhanced location chart
    $regionalDistribution = (clone $baseQuery)
        ->selectRaw('COALESCE(region, "Unknown") as region, COUNT(*) as count')
        ->groupBy('region')
        ->orderByDesc('count')
        ->limit(10)
        ->pluck('count', 'region')
        ->toArray();

    // City distribution
    $cityDistribution = (clone $baseQuery)
        ->selectRaw('COALESCE(city, "Unknown") as city, COUNT(*) as count')
        ->groupBy('city')
        ->orderByDesc('count')
        ->limit(10)
        ->pluck('count', 'city')
        ->toArray();

    // Business type distribution
    $businessTypeDistribution = (clone $baseQuery)
        ->selectRaw('COALESCE(business_type, "Unknown") as business_type, COUNT(*) as count')
        ->groupBy('business_type')
        ->orderByDesc('count')
        ->limit(8)
        ->pluck('count', 'business_type')
        ->toArray();

    // Monthly installation trends (last 6 months)
    $installationTrends = [];
    $serviceTrends = [];

    for ($i = 5; $i >= 0; $i--) {
        $startOfMonth = now()->subMonths($i)->startOfMonth();
        $endOfMonth = now()->subMonths($i)->endOfMonth();

        $monthName = $startOfMonth->format('M');

        $installationTrends[$monthName] = (clone $baseQuery)
            ->whereNotNull('installation_date')
            ->whereBetween('installation_date', [$startOfMonth, $endOfMonth])
            ->count();

        $serviceTrends[$monthName] = (clone $baseQuery)
            ->whereNotNull('last_service_date')
            ->whereBetween('last_service_date', [$startOfMonth, $endOfMonth])
            ->count();
    }

    // Performance metrics
    $uptimePercentage = $totalTerminals > 0 ? round(($activeTerminals / $totalTerminals) * 100, 1) : 0;
    $serviceComplianceRate = $totalTerminals > 0 ? round(($recentlyServiced / $totalTerminals) * 100, 1) : 0;

    // Coverage metrics by region
    $coverageMetrics = (clone $baseQuery)
        ->selectRaw('
            region,
            COUNT(*) as total,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN last_service_date >= ? THEN 1 ELSE 0 END) as recently_serviced
        ', [now()->subDays(90)])
        ->groupBy('region')
        ->get()
        ->map(function($item) {
            return [
                'region' => $item->region ?: 'Unknown',
                'total' => $item->total,
                'active' => $item->active,
                'uptime_rate' => $item->total > 0 ? round(($item->active / $item->total) * 100, 1) : 0,
                'service_rate' => $item->total > 0 ? round(($item->recently_serviced / $item->total) * 100, 1) : 0,
            ];
        })
        ->toArray();

    return [
        // Basic stats (for the 4 main cards)
        'total_terminals' => $totalTerminals,
        'active_terminals' => $activeTerminals,
        'offline_terminals' => $offlineTerminals,
        'faulty_terminals' => $faultyTerminals,

        // Service timeline stats
        'recently_serviced' => $recentlyServiced,
        'service_due' => $serviceDue,
        'overdue_service' => $overdueService,
        'never_serviced' => $neverServiced,

        // Installation & trends
        'recent_installations' => $recentInstallations,
        'installation_trends' => $installationTrends,
        'service_trends' => $serviceTrends,

        // Distribution data for charts
        'model_distribution' => $modelDistribution,
        'client_distribution' => $clientDistribution,
        'regional_distribution' => $regionalDistribution,
        'city_distribution' => $cityDistribution,
        'business_type_distribution' => $businessTypeDistribution,

        // Performance metrics
        'uptime_percentage' => $uptimePercentage,
        'service_compliance_rate' => $serviceComplianceRate,
        'coverage_metrics' => $coverageMetrics,

        // Additional useful metrics
        'avg_terminals_per_region' => count($regionalDistribution) > 0 ? round($totalTerminals / count($regionalDistribution), 1) : 0,
        'most_common_model' => !empty($modelDistribution) ? array_keys($modelDistribution)[0] : 'N/A',
        'largest_client' => !empty($clientDistribution) ? array_keys($clientDistribution)[0] : 'N/A',

        // Chart-specific calculated data
        'chart_data' => [
            'status_colors' => [
                'active' => '#28a745',
                'offline' => '#ffc107',
                'faulty' => '#dc3545',
                'maintenance' => '#17a2b8'
            ],
            'total_clients' => count($clientDistribution),
            'total_regions' => count($regionalDistribution),
            'total_models' => count($modelDistribution),
        ]
    ];

}
}
