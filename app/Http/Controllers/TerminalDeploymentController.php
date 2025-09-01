<?php

namespace App\Http\Controllers;

use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Project;
use App\Models\Employee;
use App\Models\JobAssignment;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;



class TerminalDeploymentController extends Controller
{
    /**
     * Display the hierarchical terminal deployment page
     */
    public function index()
{
    try {
        // Debug step by step
        \Log::info('=== DEPLOYMENT CONTROLLER DEBUG START ===');

        // Test basic database connection
        $totalClients = Client::count();
        \Log::info('Total clients in database: ' . $totalClients);

        // Test active clients query
        $activeClients = Client::where('status', 'active')->get();
        \Log::info('Active clients found: ' . $activeClients->count());
        \Log::info('Active clients data: ', $activeClients->toArray());

        // Test relationship
        $clientsWithCount = Client::where('status', 'active')
            ->withCount('posTerminals')
            ->get();
        \Log::info('Clients with terminal count: ', $clientsWithCount->toArray());

        // Your original mapping
       $clients = Client::withCount('posTerminals')
            ->withCount('posTerminals')
            ->orderBy('company_name')
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->company_name,
                    'terminal_count' => $client->pos_terminals_count
                ];
            });

        \Log::info('Final clients array: ', $clients->toArray());

        // Test technicians
        $technicians = Employee::active()
            ->fieldTechnicians()
            ->with('role')
            ->get();

        \Log::info('Technicians found: ' . $technicians->count());

        // Simple stats for now
        $stats = [
            'total_terminals' => PosTerminal::count(),
            'active_clients' => Client::where('status', 'active')->count(),
        ];

        \Log::info('Stats: ', $stats);
        \Log::info('=== DEPLOYMENT CONTROLLER DEBUG END ===');

        return view('deployment.hierarchical', compact('stats', 'clients', 'technicians'));

    } catch (\Exception $e) {
        \Log::error('Error in deployment controller: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        // Return a simple debug view
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

    private function convertHierarchyToArray($hierarchy)
    {
        $result = [];

        foreach ($hierarchy as $province) {
            $provinceData = [
                'id' => $province['id'],
                'name' => $province['name'],
                'type' => 'province',
                'terminal_count' => $province['terminal_count'],
                'assigned_count' => $province['assigned_count'],
                'children' => [],
                'collapsed' => $province['collapsed']
            ];

            foreach ($province['children'] as $city) {
                $cityData = [
                    'id' => $city['id'],
                    'name' => $city['name'],
                    'type' => 'city',
                    'terminal_count' => $city['terminal_count'],
                    'assigned_count' => $city['assigned_count'],
                    'children' => [],
                    'collapsed' => $city['collapsed']
                ];

                foreach ($city['children'] as $region) {
                    $regionData = [
                        'id' => $region['id'],
                        'name' => $region['name'],
                        'type' => 'region',
                        'terminal_count' => $region['terminal_count'],
                        'assigned_count' => $region['assigned_count'],
                        'terminals' => $region['terminals'],
                        'collapsed' => $region['collapsed']
                    ];

                    $cityData['children'][] = $regionData;
                }

                $provinceData['children'][] = $cityData;
            }

            $result[] = $provinceData;
        }

        return $result;
    }


    /**
     * Quick assign single terminal via drag & drop
     */
    public function quickAssignTerminal(Request $request)
    {
        $request->validate([
            'terminal_id' => 'required|exists:pos_terminals,id',
            'technician_id' => 'required|exists:employees,id',
            'priority' => 'in:low,normal,high,emergency',
            'scheduled_date' => 'nullable|date|after_or_equal:today'
        ]);

        DB::beginTransaction();
        try {
            // Verify technician is field technician
            $technician = Employee::active()->fieldTechnicians()->findOrFail($request->technician_id);
            $terminal = PosTerminal::findOrFail($request->terminal_id);

            // Check if terminal is already assigned
            $alreadyAssigned = JobAssignment::where('status', '!=', 'cancelled')
                ->whereJsonContains('pos_terminals', $terminal->id)
                ->exists();

            if ($alreadyAssigned) {
                throw new \Exception('Terminal is already assigned to an active job');
            }

            $scheduledDate = $request->scheduled_date ?? now()->addDay()->format('Y-m-d');

            $assignment = JobAssignment::create([
                'assignment_id' => JobAssignment::generateAssignmentId(),
                'technician_id' => $request->technician_id,
                'region_id' => $terminal->region_id,
                'client_id' => $terminal->client_id,
                'pos_terminals' => [$terminal->id],
                'scheduled_date' => $scheduledDate,
                'service_type' => $request->service_type ?? 'general_service',
                'priority' => $request->priority ?? 'normal',
                'status' => 'assigned',
                'notes' => 'Quick assignment via drag & drop',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Terminal assigned successfully!',
                'assignment_id' => $assignment->assignment_id,
                'technician_name' => $technician->full_name
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in quick assign: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error assigning terminal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-assign terminals using AI algorithm
     */
    public function autoAssignTerminals(Request $request)
    {
        $request->validate([
            'client_ids' => 'array',
            'client_ids.*' => 'exists:clients,id',
            'project_ids' => 'array',
            'project_ids.*' => 'exists:projects,id'
        ]);

        DB::beginTransaction();
        try {
            $clientIds = $request->get('client_ids', []);
            $projectIds = $request->get('project_ids', []);

            // Get unassigned terminals
            $assignedTerminalIds = JobAssignment::where('status', '!=', 'cancelled')
                ->whereJsonLength('pos_terminals', '>', 0)
                ->get()
                ->pluck('pos_terminals')
                ->flatten()
                ->unique()
                ->toArray();

            $query = PosTerminal::whereNotIn('id', $assignedTerminalIds);

            if (!empty($clientIds)) {
                $query->whereIn('client_id', $clientIds);
            }

            $unassignedTerminals = $query->with(['client'])->get();

            // Get available technicians
            $availableTechnicians = Employee::active()
                ->fieldTechnicians()
                ->get()
                ->filter(function($tech) {
                    return $this->getTechnicianAvailability($tech) !== 'overloaded';
                });

            if ($availableTechnicians->isEmpty()) {
                throw new \Exception('No available technicians for auto-assignment');
            }

            $assignmentsCreated = 0;
            $terminalsPerAssignment = 3; // Max terminals per assignment

            // Group terminals by location for efficient routing
            $terminalGroups = $unassignedTerminals->groupBy(function($terminal) {
                return $terminal->province . '|' . $terminal->city;
            });

            foreach ($terminalGroups as $location => $terminals) {
                // Find best technician for this location (simple round-robin for now)
                $bestTechnician = $availableTechnicians->sortBy('current_workload')->first();

                if (!$bestTechnician) {
                    break;
                }

                // Create assignments in chunks
                $terminalChunks = $terminals->chunk($terminalsPerAssignment);

                foreach ($terminalChunks as $chunk) {
                    if ($chunk->isEmpty()) continue;

                    $firstTerminal = $chunk->first();

                    $assignment = JobAssignment::create([
                        'assignment_id' => JobAssignment::generateAssignmentId(),
                        'technician_id' => $bestTechnician->id,
                        'region_id' => $firstTerminal->region_id,
                        'client_id' => $firstTerminal->client_id,
                        'project_id' => !empty($projectIds) ? $projectIds[0] : null,
                        'pos_terminals' => $chunk->pluck('id')->toArray(),
                        'scheduled_date' => now()->addDays(rand(1, 7))->format('Y-m-d'),
                        'service_type' => 'general_service',
                        'priority' => 'normal',
                        'status' => 'assigned',
                        'notes' => 'Auto-assigned by system based on location optimization',
                        'created_by' => auth()->id()
                    ]);

                    $assignmentsCreated++;

                    $workload = $workload ?? [];
$workload[$bestTechnician->id] = ($workload[$bestTechnician->id] ?? 0) + $chunk->count();

                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Auto-assignment completed successfully!",
                'assignments_created' => $assignmentsCreated,
                'terminals_assigned' => $unassignedTerminals->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in auto-assign: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error in auto-assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export assignments in various formats
     */
    public function exportAssignments(Request $request, $format)
    {
        try {
            $clientIds = $request->get('client_ids', []);
            $projectIds = $request->get('project_ids', []);

            // Get assignments based on filters
            $query = JobAssignment::with(['technician', 'client', 'project'])
                ->where('status', '!=', 'cancelled');

            if (!empty($clientIds)) {
                $query->whereIn('client_id', $clientIds);
            }

            if (!empty($projectIds)) {
                $query->whereIn('project_id', $projectIds);
            }

            $assignments = $query->get();

            switch ($format) {
                case 'pdf':
                    return $this->exportToPDF($assignments);
                case 'excel':
                    return $this->exportToExcel($assignments);
                case 'mobile':
                    return $this->exportForMobile($assignments);
                default:
                    return response()->json(['error' => 'Invalid export format'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /**
     * Bulk assign multiple selections
     */
    public function bulkAssign(Request $request)
{
    $request->validate([
        'technician_id' => 'required|exists:employees,id',
        'terminal_ids'  => 'required|array|min:1',
        'terminal_ids.*'=> 'exists:pos_terminals,id',
        'scheduled_date'=> 'required|date|after_or_equal:today',
        'service_type' => 'required|string|in:routine_maintenance,emergency_repair,software_update,hardware_replacement,network_configuration,installation,decommission',
        'priority'      => 'required|in:low,normal,high,emergency',
        'project_id'    => 'nullable|exists:projects,id',
        'notes'         => 'nullable|string|max:1000',
    ]);

    // Just forward to createAssignment with normalized keys
    $request->merge([
        'selected_terminals' => $request->terminal_ids,
    ]);

    return $this->createAssignment($request);
}



    private function generateNodeId($type, $name)
    {
        return $type . '-' . str_replace([' ', '/', '|'], '-', strtolower($name));
    }

    /**
     * Get project information for a terminal
     */
    private function getTerminalProjects($terminal, $projectIds, $projects)
    {
        $terminalProjects = [];

        if (!empty($projectIds)) {
            // Get assignments for this terminal with these projects
            $assignments = JobAssignment::where('pos_terminals', 'like', '%"' . $terminal->id . '"%')
                ->whereIn('project_id', $projectIds)
                ->with('project')
                ->get();

            foreach ($assignments as $assignment) {
                if ($assignment->project) {
                    $terminalProjects[] = [
                        'id' => $assignment->project->id,
                        'name' => $assignment->project->project_name,
                        'type' => strtolower($assignment->project->project_type),
                        'badge' => $this->getProjectTypeBadge($assignment->project->project_type)
                    ];
                }
            }

            // If no specific assignments, check if terminal's client has these projects
            if (empty($terminalProjects)) {
                $clientProjects = $terminal->client->projects()
                    ->whereIn('id', $projectIds)
                    ->get();

                foreach ($clientProjects as $project) {
                    $terminalProjects[] = [
                        'id' => $project->id,
                        'name' => $project->project_name,
                        'type' => strtolower($project->project_type),
                        'badge' => $this->getProjectTypeBadge($project->project_type)
                    ];
                }
            }
        }

        return array_unique($terminalProjects, SORT_REGULAR);
    }

    /**
     * Get project type badge class
     */
    private function getProjectTypeBadge($projectType)
    {
        $badges = [
            'discovery' => 'project-discovery',
            'servicing' => 'project-servicing',
            'support' => 'project-support',
            'maintenance' => 'project-maintenance'
        ];

        return $badges[strtolower($projectType)] ?? 'project-discovery';
    }

    /**
     * Get status badge CSS class
     */
    private function getStatusBadgeClass($status)
    {
        return 'status-' . strtolower($status);
    }

    /**
     * Get formatted last service information
     */
    private function getLastServiceInfo($terminal)
    {
        if (!$terminal->last_service_date) {
            return [
                'date' => null,
                'formatted' => 'Never serviced',
                'days_ago' => null,
                'relative' => 'Never'
            ];
        }

        $lastService = Carbon::parse($terminal->last_service_date);
        $daysSince = $lastService->diffInDays(now());

        return [
            'date' => $lastService->format('Y-m-d'),
            'formatted' => $lastService->format('M j, Y'),
            'days_ago' => $daysSince,
            'relative' => $lastService->diffForHumans()
        ];
    }

    /**
     * Check if terminal needs service
     */
    private function terminalNeedsService($terminal)
    {
        if (!$terminal->last_service_date) {
            return true; // Never serviced
        }

        $lastService = Carbon::parse($terminal->last_service_date);
        $monthsSinceService = $lastService->diffInMonths(now());

        // Define service intervals by terminal type or use default 6 months
        $serviceIntervalMonths = 6;

        return $monthsSinceService >= $serviceIntervalMonths;
    }

    /**
     * Get terminal priority based on various factors
     */
    private function getTerminalPriority($terminal)
    {
        // High priority if faulty or overdue
        if ($terminal->current_status === 'faulty') {
            return 'high';
        }

        if ($this->terminalNeedsService($terminal)) {
            return 'normal';
        }

        return 'low';
    }

    /**
     * Get technician availability status
     */
    private function getTechnicianAvailability($technician)
    {
        $todayAssignments = $technician->jobAssignments()
            ->whereDate('scheduled_date', today())
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();

        if ($todayAssignments === 0) {
            return 'available';
        } elseif ($todayAssignments < 5) {
            return 'busy';
        } else {
            return 'overloaded';
        }
    }

    /**
     * Get summary statistics for the hierarchy
     */
    private function getHierarchySummary($terminals)
    {
        $assignedTerminalIds = JobAssignment::where('status', '!=', 'cancelled')
            ->whereJsonLength('pos_terminals', '>', 0)
            ->get()
            ->pluck('pos_terminals')
            ->flatten()
            ->unique()
            ->toArray();

        $assignedCount = $terminals->whereIn('id', $assignedTerminalIds)->count();

        return [
            'total_terminals' => $terminals->count(),
            'assigned_terminals' => $assignedCount,
            'unassigned_terminals' => $terminals->count() - $assignedCount,
            'provinces' => $terminals->pluck('province')->unique()->filter()->count(),
            'cities' => $terminals->pluck('city')->unique()->filter()->count(),
            'clients' => $terminals->pluck('client_id')->unique()->count(),
            'status_breakdown' => $terminals->groupBy('current_status')->map->count(),
            'needs_service' => $terminals->filter(function($terminal) {
                return $this->terminalNeedsService($terminal);
            })->count()
        ];
    }

    /**
     * Get empty summary for when no terminals are found
     */
    private function getEmptySummary()
    {
        return [
            'total_terminals' => 0,
            'assigned_terminals' => 0,
            'unassigned_terminals' => 0,
            'provinces' => 0,
            'cities' => 0,
            'clients' => 0,
            'status_breakdown' => [],
            'needs_service' => 0
        ];
    }

    /**
     * Export to PDF format
     */
    private function exportToPDF($assignments)
    {
        // Implementation for PDF export
        // You can use libraries like DomPDF or similar
        return response()->json(['message' => 'PDF export feature coming soon']);
    }

    /**
     * Export to Excel format
     */
    private function exportToExcel($assignments)
    {
        // Implementation for Excel export
        // You can use libraries like Laravel Excel
        return response()->json(['message' => 'Excel export feature coming soon']);
    }

    /**
     * Export for mobile sync
     */
    private function exportForMobile($assignments)
    {
        // Return JSON format suitable for mobile app sync
        return response()->json([
            'assignments' => $assignments->map(function($assignment) {
                return [
                    'id' => $assignment->assignment_id,
                    'technician' => $assignment->technician->full_name,
                    'client' => $assignment->client->company_name,
                    'project' => $assignment->project?->project_name,
                    'terminals' => $assignment->pos_terminals,
                    'scheduled_date' => $assignment->scheduled_date,
                    'service_type' => $assignment->service_type,
                    'priority' => $assignment->priority,
                    'status' => $assignment->status,
                    'notes' => $assignment->notes
                ];
            }),
            'exported_at' => now()->toISOString()
        ]);
    }



public function getHierarchicalTerminals(Request $request)
{
    $request->validate([
        'client_ids' => 'array',
        'client_ids.*' => 'exists:clients,id',
        'project_ids' => 'array',
        'project_ids.*' => 'exists:projects,id',
        'start_date' => 'nullable|date',
        'status_filter' => 'nullable|in:active,offline,maintenance,faulty'
    ]);

    try {
        $clientIds = $request->get('client_ids', []);

        if (empty($clientIds)) {
            return response()->json([
                'success' => true,
                'hierarchy' => [],
                'total_terminals' => 0,
                'summary' => $this->getEmptySummary()
            ]);
        }

        // Simple, clean query
        $query = PosTerminal::with(['client:id,company_name', 'regionModel:id,name'])
            ->whereIn('client_id', $clientIds);

        // Apply status filter if provided
        if ($request->filled('status_filter')) {
            $query->where('current_status', $request->status_filter);
        }

        $terminals = $query->get();

        // Build hierarchy
        $hierarchy = $this->buildTerminalHierarchy($terminals, $request->get('project_ids', []));

        return response()->json([
            'success' => true,
            'hierarchy' => $hierarchy,
            'total_terminals' => $terminals->count(),
            'summary' => $this->getHierarchySummary($terminals)
        ]);

    } catch (\Exception $e) {
        Log::error('Error in getHierarchicalTerminals: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading terminal hierarchy: ' . $e->getMessage()
        ], 500);
    }
}

private function buildTerminalHierarchy($terminals, $projectIds = [])
{
    $hierarchy = [];

    // Get assigned terminal IDs for status checking
    $assignedTerminalIds = JobAssignment::where('status', '!=', 'cancelled')
        ->get()
        ->pluck('pos_terminals')
        ->flatten()
        ->filter()
        ->unique()
        ->toArray();

    foreach ($terminals as $terminal) {
        $province = $terminal->province ?: 'Unknown Province';
        $city = $terminal->city ?: 'Unknown City';
        $region = $terminal->regionModel?->name ?: $terminal->area ?: 'Unknown Region';

        // Initialize hierarchy levels
        if (!isset($hierarchy[$province])) {
            $hierarchy[$province] = [
                'id' => 'province-' .  Str::slug($province),
                'name' => strtoupper($province),
                'type' => 'province',
                'terminal_count' => 0,
                'assigned_count' => 0,
                'children' => [],
                'collapsed' => false
            ];
        }

        if (!isset($hierarchy[$province]['children'][$city])) {
            $hierarchy[$province]['children'][$city] = [
                'id' => 'city-' . \Str::slug($city . '-' . $province),
                'name' => $city,
                'type' => 'city',
                'terminal_count' => 0,
                'assigned_count' => 0,
                'children' => [],
                'collapsed' => false
            ];
        }

        if (!isset($hierarchy[$province]['children'][$city]['children'][$region])) {
            $hierarchy[$province]['children'][$city]['children'][$region] = [
                'id' => 'region-' . \Str::slug($region . '-' . $city),
                'name' => $region,
                'type' => 'region',
                'terminal_count' => 0,
                'assigned_count' => 0,
                'terminals' => [],
                'collapsed' => false
            ];
        }

        $isAssigned = in_array($terminal->id, $assignedTerminalIds);

        // Add terminal data
        $terminalData = [
            'id' => 'terminal-' . $terminal->id,
            'terminal_id' => $terminal->terminal_id,
            'merchant_name' => $terminal->merchant_name ?: 'Unknown Merchant',
            'client' => [
                'id' => $terminal->client->id,
                'name' => $terminal->client->company_name
            ],
            'address' => $terminal->physical_address,
            'city' => $terminal->city,
            'province' => $terminal->province,
            'area' => $terminal->area,
            'status' => $terminal->current_status,
            'phone' => $terminal->merchant_phone,
            'projects' => [], // Add projects if needed
            'is_assigned' => $isAssigned,
            'type' => 'terminal'
        ];

        $hierarchy[$province]['children'][$city]['children'][$region]['terminals'][] = $terminalData;

        // Update counters
        $hierarchy[$province]['terminal_count']++;
        $hierarchy[$province]['children'][$city]['terminal_count']++;
        $hierarchy[$province]['children'][$city]['children'][$region]['terminal_count']++;

        if ($isAssigned) {
            $hierarchy[$province]['assigned_count']++;
            $hierarchy[$province]['children'][$city]['assigned_count']++;
            $hierarchy[$province]['children'][$city]['children'][$region]['assigned_count']++;
        }
    }

    return $this->convertHierarchyToArray($hierarchy);
}

// Fix 3: Simplified project association
private function getTerminalProjectsSimple($terminal, $projectIds, $projects)
{
    $terminalProjects = [];

    // If specific projects selected, only show those
    if (!empty($projectIds)) {
        // Check if terminal's client has these projects
        $clientProjects = $terminal->client->projects()
            ->whereIn('id', $projectIds)
            ->get();

        foreach ($clientProjects as $project) {
            $terminalProjects[] = [
                'id' => $project->id,
                'name' => $project->project_name,
                'type' => strtolower($project->project_type),
                'color_class' => $this->getProjectColorClass($project->project_type)
            ];
        }
    }

    return $terminalProjects;
}

// Fix 4: Add project color class method
private function getProjectColorClass($projectType)
{
    $colors = [
        'discovery' => 'project-discovery',    // Blue
        'servicing' => 'project-servicing',   // Green
        'support' => 'project-support',       // Orange
        'maintenance' => 'project-maintenance', // Purple
        'installation' => 'project-installation' // Primary
    ];

    return $colors[strtolower($projectType)] ?? 'project-discovery';
}
public function getInitialData()
{
    try {
        // Get active clients for the filter dropdown
        $clients = Client::where('status', 'active')
            ->withCount('posTerminals')
            ->orderBy('company_name')
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->company_name,
                    'terminal_count' => $client->pos_terminals_count
                ];
            });

        // Get available technicians for assignment
        $technicians = Employee::active()
            ->fieldTechnicians()
            ->with('role')
            ->get()
            ->map(function($technician) {
                return [
                    'id' => $technician->id,
                    'name' => $technician->full_name,
                    'specialization' => $technician->getSpecialization(),
                    'phone' => $technician->phone,
                    'availability_status' => $this->getTechnicianAvailability($technician)
                ];
            });

        return response()->json([
            'success' => true,
            'clients' => $clients,
            'technicians' => $technicians
        ]);

    } catch (\Exception $e) {
        Log::error('Error loading initial data: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error loading initial data: ' . $e->getMessage()
        ], 500);
    }
}



public function getProjectsByClients(Request $request)
{
    try {
        $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id'
        ]);

        $clientIds = $request->get('client_ids', []);

        if (empty($clientIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No clients selected',
                'projects' => []
            ]);
        }

        $projects = Project::whereIn('client_id', $clientIds)
            ->where('status', 'active')
            ->with('client:id,company_name')
            ->orderBy('project_name')
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'display_name' => $project->client->company_name . ' - ' . $project->project_name,
                    'client_id' => $project->client_id,
                    'client_name' => $project->client->company_name,
                    'project_type' => $project->project_type,
                    'badge' => $this->getProjectTypeBadge($project->project_type),
                    'start_date' => $project->start_date?->format('Y-m-d'),
                    'end_date' => $project->end_date?->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'projects' => $projects,
            'message' => 'Projects loaded successfully'
        ]);

    } catch (\Exception $e) {
        Log::error('Error loading projects: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error loading projects: ' . $e->getMessage(),
            'projects' => []
        ], 500);
    }
}
public function createProject(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string|in:discovery,servicing,support,maintenance,installation',
        'duration' => 'required|integer',
        'description' => 'nullable|string',
        'client_ids' => 'required|array',
        'client_ids.*' => 'exists:clients,id'
    ]);

    DB::beginTransaction();
    try {
        // Generate a unique project code
        $projectCode = 'PROJ-' . strtoupper(substr($request->type, 0, 3)) . '-' . date('Ymd') . '-' . rand(100, 999);

        $project = Project::create([
            'project_name' => $request->name,
            'project_code' => $projectCode, // Add this line
            'project_type' => $request->type,
            'client_id' => $request->client_ids[0],
            'description' => $request->description,
            'status' => 'active',
            'start_date' => now(),
            'expected_duration_months' => $request->duration,
            'created_by' => auth()->id()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'project' => [
                'id' => $project->id,
                'name' => $project->project_name,
                'type' => $project->project_type
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating project: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error creating project: ' . $e->getMessage()
        ], 500);
    }
}

private function getOrCreateRegionId($terminal)
{
    // First, try to use the terminal's existing region_id
    if ($terminal->region_id) {
        return $terminal->region_id;
    }

    // If no region_id, try to find or create based on area/location
    $regionName = $terminal->area ?: $terminal->city ?: 'Unknown Region';

    // Try to find existing region
    $region = Region::where('name', $regionName)->first();

    if (!$region) {
        // Create a new region
        $region = Region::create([
            'name' => $regionName,
            'province' => $terminal->province ?: 'Unknown Province',
            'status' => 'active',
            'created_by' => auth()->id()
        ]);
    }

    // Update the terminal with the region_id for future use
    $terminal->update(['region_id' => $region->id]);

    return $region->id;
}
// app/Http/Controllers/TerminalDeploymentController.php

public function createAssignment(Request $request)
{
    $request->validate([
        'technician_id'      => 'required|exists:employees,id',
        'project_id'         => 'nullable|exists:projects,id',
        'selected_terminals' => 'required|array|min:1',
        'selected_terminals.*' => 'integer|exists:pos_terminals,id',
        'scheduled_date'     => 'required|date|after_or_equal:today',
        'service_type' => 'required|string|in:routine_maintenance,emergency_repair,software_update,hardware_replacement,network_configuration,installation,decommission',
        'priority'           => 'required|in:low,normal,high,emergency',
        'notes'              => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();

    try {
        // ensure unique ids
        $terminalIds = array_values(array_unique($request->selected_terminals));

        // Check for existing assignments and track history
        $existingAssignments = JobAssignment::where('status', '!=', 'cancelled')
            ->get()
            ->filter(function($assignment) use ($terminalIds) {
                return collect($assignment->pos_terminals)->intersect($terminalIds)->isNotEmpty();
            });

        $assignmentHistory = [];
        foreach ($existingAssignments as $existing) {
            $conflictingTerminals = collect($existing->pos_terminals)->intersect($terminalIds);

            foreach ($conflictingTerminals as $terminalId) {
                $assignmentHistory[] = [
                    'terminal_id' => $terminalId,
                    'previous_assignment_id' => $existing->assignment_id,
                    'previous_technician' => $existing->technician->full_name ?? 'Unknown',
                    'previous_date' => $existing->scheduled_date,
                    'previous_service_type' => $existing->service_type,
                    'previous_status' => $existing->status,
                    'reassignment_date' => now()
                ];
            }
        }

        // use the first terminal for region/client context
        $first = PosTerminal::with('client')->findOrFail($terminalIds[0]);

        // Create new assignment (allowing reassignment)
        $assignment = JobAssignment::create([
            'assignment_id'  => JobAssignment::generateAssignmentId(),
            'technician_id'  => (int)$request->technician_id,
            'region_id'      => $first->region_id ?: null,
            'client_id'      => $first->client_id,
            'project_id'     => $request->project_id,
            'pos_terminals'  => $terminalIds,
            'scheduled_date' => $request->scheduled_date,
            'service_type'   => $request->service_type,
            'priority'       => $request->priority,
            'status'         => 'assigned',
            'notes'          => trim(($request->notes ?? 'Assignment with possible reassignments')),
            'created_by'     => auth()->id(),
            // Store assignment history as JSON
            'assignment_history' => $assignmentHistory
        ]);

        // Optionally update previous assignments status to 'reassigned' instead of cancelling
        foreach ($existingAssignments as $existing) {
            $conflictingTerminals = collect($existing->pos_terminals)->intersect($terminalIds);
            if ($conflictingTerminals->isNotEmpty()) {
                // Update status to show it was reassigned
                $existing->update([
                    'status' => 'reassigned',
                    'notes' => $existing->notes . "\n[REASSIGNED] Terminals reassigned to assignment: " . $assignment->assignment_id . " on " . now()->format('Y-m-d H:i')
                ]);
            }
        }

        DB::commit();

        $message = 'Assignment created successfully.';
        if (!empty($assignmentHistory)) {
            $reassignedCount = count($assignmentHistory);
            $message .= " Note: {$reassignedCount} terminal(s) were reassigned from previous assignments.";
        }

        return response()->json([
            'success'           => true,
            'message'          => $message,
            'assignmentId'     => $assignment->assignment_id,
            'reassigned_count' => count($assignmentHistory),
            'assignment_history' => $assignmentHistory
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Error creating assignment with history tracking', [
            'error' => $e->getMessage(),
            'request' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error creating assignment: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Get list of assigned terminal IDs
 */
public function getAssignedTerminals()
{
    try {
        $assignedTerminalIds = JobAssignment::where('status', '!=', 'cancelled')
            ->whereJsonLength('pos_terminals', '>', 0)
            ->get()
            ->pluck('pos_terminals')
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'assigned_terminal_ids' => $assignedTerminalIds
        ]);

    } catch (\Exception $e) {
        Log::error('Error getting assigned terminals: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error loading assigned terminals',
            'assigned_terminal_ids' => []
        ], 500);
    }
}
// SiteVisitController.php
public function editTerminal($assignmentId, $terminalId)
{
    $assignment = JobAssignment::with(['technician','client','project'])
        ->findOrFail($assignmentId);

    $terminal   = PosTerminal::with('client')->findOrFail($terminalId);

    // If there’s an open/in_progress visit for this assignment+terminal, load it, else null
    $visit = \App\Models\TechnicianVisit::where('job_assignment_id', $assignment->id)
        ->where('pos_terminal_id', $terminal->id)
        ->latest('started_at')
        ->first();

    // This view contains the long form with your fields:
    return view('site_visits.edit', compact('assignment','terminal','visit'));
}

}
