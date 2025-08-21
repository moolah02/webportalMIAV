<?php
// app/Http/Controllers/DeploymentPlanningController.php

namespace App\Http\Controllers;

//use App\Models\DeploymentTemplate;
use App\Models\PosTerminal;
use App\Models\Region;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Category;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeploymentPlanningController extends Controller
{
    /**
     * Display deployment planning page
     */
   public function index()
{
    // Get regions with terminal counts
    $regions = Region::where('is_active', true)
        ->withCount(['posTerminals as active_terminals_count' => function($q) {
            $q->where('current_status', 'active');
        }])
        ->withCount(['posTerminals as total_terminals_count'])
        ->orderBy('name')
        ->get();

    // Get service types from categories
    $serviceTypes = Category::where('type', 'service_type')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

    // Get technicians
    $technicians = Employee::where('status', 'active')
        ->whereHas('role', function($q) {
            $q->whereIn('name', ['Technician', 'Field Technician', 'Maintenance']);
        })
        ->select('id', 'first_name', 'last_name', 'phone', 'role_id')
        ->with('role:id,name')
        ->orderBy('first_name')
        ->get()
        ->map(function ($t) {
            $t->name = $t->first_name.' '.$t->last_name;
            $t->specialization = $t->role->name ?? 'General';
            // optionally add placeholders for UI fields
            $t->availability_status = $t->availability_status ?? 'available';
            $t->current_workload = $t->current_workload ?? 0;
            return $t;
        });

    // Get clients
    $clients = Client::where('status', 'active')
        ->orderBy('company_name')
        ->get()
        ->map(function ($c) {
            // Blade expects name & terminal_count in your dropdown
            $c->name = $c->company_name;
            $c->terminal_count = $c->posTerminals()->count() ?? 0;
            return $c;
        });

    // Stats WITHOUT templates
    $stats = [
        'total_regions'      => $regions->count(),
        'total_terminals'    => PosTerminal::count(),
        'deployed_terminals' => PosTerminal::where('deployment_status', 'deployed')->count(),
        // you can add more non-template stats here
    ];

    return view('deployment.planning', compact(
        'regions',
        'serviceTypes',
        'technicians',
        'clients',
        'stats'
    ));
}


    /**
     * Get terminals for region with grouping by city
     */
    public function getRegionTerminals($regionId, Request $request)
    {
        try {
            $query = PosTerminal::where('region_id', $regionId);

            // Apply filters
            if ($request->get('client_id')) {
                $query->where('client_id', $request->get('client_id'));
            }

            if ($request->get('status')) {
                $query->where('current_status', $request->get('status'));
            }

            if ($request->get('deployment_status')) {
                $query->where('deployment_status', $request->get('deployment_status'));
            }

            $terminals = $query->with(['client:id,company_name', 'regionModel:id,name'])
                ->select('id', 'terminal_id', 'merchant_name', 'client_id', 'current_status', 
                        'deployment_status', 'physical_address', 'city', 'area', 'region_id')
                ->orderBy('city')
                ->orderBy('terminal_id')
                ->get();

            // Group by city
            $terminalsByCity = $terminals->groupBy('city')->map(function($cityTerminals, $city) {
                return [
                    'city' => $city ?: 'Unknown City',
                    'count' => $cityTerminals->count(),
                    'terminals' => $cityTerminals->values()
                ];
            });

            return response()->json([
                'success' => true,
                'terminals' => $terminals,
                'terminals_by_city' => $terminalsByCity,
                'total_count' => $terminals->count(),
                'cities_count' => $terminalsByCity->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading region terminals for deployment', [
                'region_id' => $regionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading terminals: ' . $e->getMessage()
            ], 500);
        }
    }

    
// In your store method, update the validation and creation
/* public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name'            => 'required|string|max:255|unique:deployment_templates,template_name',
            'client_id'                => 'required|exists:clients,id', 
            'pos_terminals'            => 'required|string|min:3',
            'group_by'                 => 'required|in:city,province,area,address',
            'location_value'           => 'required|string',
            'service_type'             => 'nullable|string',
            'priority'                 => 'nullable|in:low,normal,high,emergency',
            'estimated_duration_hours' => 'nullable|numeric|min:0.5|max:12',
            'description'              => 'nullable|string|max:500',
            'notes'                    => 'nullable|string|max:1000',
            'tags'                     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Decode terminals
            $terminals = json_decode($request->get('pos_terminals'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE || empty($terminals)) {
                throw new \Exception('Invalid terminals selection');
            }

            // Get the region from the first terminal
            $firstTerminal = PosTerminal::where('client_id', $request->client_id)
                ->whereIn('id', $terminals)
                ->first();

            if (!$firstTerminal) {
                throw new \Exception('No valid terminals found for the selected client');
            }

            // Parse tags
            $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

            // Create template
            $template = DeploymentTemplate::create([
                'template_name'            => $request->template_name,
                'region_id'                => $firstTerminal->region_id,
                'group_by'                 => $request->group_by,
                'description'              => $request->description,
                'pos_terminals'            => $terminals,
                'service_type'             => $request->service_type ?? 'routine_maintenance',
                'priority'                 => $request->priority ?? 'normal',
                'estimated_duration_hours' => $request->estimated_duration_hours,
                'notes'                    => $request->notes,
                'tags'                     => $tags,
                'is_active'                => true,
                'created_by'               => auth()->id() ?? 1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deployment template created successfully!',
                'template' => $template->load(['region', 'creator'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating deployment template', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['_token'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating template: ' . $e->getMessage()
            ], 500);
        }
    }

// Add this method to your DeploymentPlanningController
public function getClientLocations($clientId, $locationType)
{
    try {
        \Log::info("Getting {$locationType} for client: " . $clientId);
        
        // Map the location type to the correct database field
        $fieldMap = [
            'cities' => 'city',
            'provinces' => 'province', 
            'areas' => 'area',
            'addresses' => 'physical_address'
        ];
        
        if (!isset($fieldMap[$locationType])) {
            return response()->json([
                'error' => 'Invalid location type'
            ], 400);
        }
        
        $field = $fieldMap[$locationType];
        
        $locations = PosTerminal::where('client_id', $clientId)
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->distinct()
            ->orderBy($field)
            ->pluck($field)
            ->filter()
            ->values();

        \Log::info("Found {$locationType}: " . $locations->count());

        return response()->json($locations);

    } catch (\Exception $e) {
        \Log::error("Error loading {$locationType}: " . $e->getMessage());
        
        return response()->json([
            'error' => "Error loading {$locationType}: " . $e->getMessage()
        ], 500);
    }
}
   
    public function show($templateId)
    {
        try {
            $template = DeploymentTemplate::with(['region', 'creator'])
                ->findOrFail($templateId);

            // Get terminal details
            $terminals = PosTerminal::whereIn('id', $template->pos_terminals)
                ->with(['client:id,company_name', 'regionModel:id,name'])
                ->get();

            // Group by city
            $terminalsByCity = $terminals->groupBy('city');

            // Get recent deployments using this template
            $recentDeployments = JobAssignment::where('template_id', $templateId)
                ->with(['technician:id,first_name,last_name', 'region:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'template' => $template,
                'terminals' => $terminals,
                'terminals_by_city' => $terminalsByCity,
                'recent_deployments' => $recentDeployments,
                'stats' => [
                    'terminals_count' => $terminals->count(),
                    'cities_count' => $terminalsByCity->count(),
                    'clients_count' => $terminals->pluck('client_id')->unique()->count(),
                    'deployments_count' => $recentDeployments->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading template details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deploy template as job assignment
     */
    /*public function deploy(Request $request, $templateId)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:employees,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'additional_notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $template = DeploymentTemplate::findOrFail($templateId);
            
            if (!$template->canBeDeployed()) {
                throw new \Exception('Template cannot be deployed (inactive or has no terminals)');
            }

            // Check if technician is available on the scheduled date
            $existingAssignment = JobAssignment::where('technician_id', $request->technician_id)
                ->whereDate('scheduled_date', $request->scheduled_date)
                ->where('status', 'assigned')
                ->first();

            if ($existingAssignment) {
                throw new \Exception('Technician already has an assignment on this date');
            }

            // Deploy template as assignment
            $assignment = $template->deployAsAssignment(
                $request->technician_id,
                $request->scheduled_date,
                $request->additional_notes
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Template deployed successfully!',
                'assignment_id' => $assignment->assignment_id,
                'assignment' => $assignment->load(['technician', 'region'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error deploying template: ' . $e->getMessage()
            ], 500);
        }
    }*/

    /**
     * Update template
     */
   /* public function update(Request $request, $templateId)
    {
        $template = DeploymentTemplate::findOrFail($templateId);

        $validator = Validator::make($request->all(), [
    'template_name'            => "required|string|max:255|unique:deployment_templates,template_name,{$templateId}",
    'description'              => 'nullable|string|max:500',
    'estimated_duration_hours' => 'nullable|numeric|min:0.5|max:12',
    'priority'                 => 'required|in:low,normal,high,emergency',
    'group_by'                 => 'required|in:region,city,address',   // ← add here
    'notes'                    => 'nullable|string|max:1000',
    'tags'                     => 'nullable|string',
    'is_active'                => 'boolean',
]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tags = $request->tags ? array_map('trim', explode(',', $request->tags)) : [];

            $template->update([
                'template_name' => $request->template_name,
                'description' => $request->description,
                'estimated_duration_hours' => $request->estimated_duration_hours,
                'priority' => $request->priority,
                'notes' => $request->notes,
                'tags' => $tags,
                'group_by' => $request->group_by,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully!',
                'template' => $template->fresh(['region', 'creator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating template: ' . $e->getMessage()
            ], 500);
        }
    }*/

    /**
     * Delete template
     */
    /*public function destroy($templateId)
    {
        try {
            $template = DeploymentTemplate::findOrFail($templateId);
            
            // Check if template has been used in assignments
            $assignmentsCount = JobAssignment::where('template_id', $templateId)->count();
            
            if ($assignmentsCount > 0) {
                // Soft delete by deactivating instead of hard delete
                $template->update(['is_active' => false]);
                $message = 'Template deactivated successfully (has been used in assignments)';
            } else {
                $template->delete();
                $message = 'Template deleted successfully!';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting template: ' . $e->getMessage()
            ], 500);
        }
    }*/

    /**
     * Export templates
     */
    /*public function export(Request $request)
    {
        $query = DeploymentTemplate::with(['region', 'creator']);
        
        if ($request->get('region_id')) {
            $query->where('region_id', $request->get('region_id'));
        }
        
        if ($request->get('is_active') !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        
        $templates = $query->orderBy('template_name')->get();
        
        $filename = 'deployment-templates-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($templates) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Template Name', 'Region', 'Service Type', 'Priority', 
                'Terminals Count', 'Est. Duration (hrs)', 'Cities Covered',
                'Status', 'Created By', 'Created Date', 'Tags'
            ]);
            
            foreach ($templates as $template) {
                fputcsv($file, [
                    $template->template_name,
                    $template->region->name ?? 'N/A',
                    $template->service_type_display,
                    ucfirst($template->priority),
                    $template->terminals_count,
                    $template->estimated_duration_hours,
                    implode(', ', $template->getCitiesCovered()->toArray()),
                    $template->is_active ? 'Active' : 'Inactive',
                    $template->creator ? $template->creator->name : 'N/A',
                    $template->created_at->format('Y-m-d'),
                    implode(', ', $template->tags ?? [])
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }*/

    /**
     * Get deployment analytics
     */
    /*public function analytics(Request $request)
    {
        try {
            $period = $request->get('period', '30'); // days
            $startDate = Carbon::now()->subDays($period);

            // Templates by region
            $templatesByRegion = DeploymentTemplate::select('region_id')
                ->with('region:id,name')
                ->where('is_active', true)
                ->get()
                ->groupBy('region.name')
                ->map->count();

            // Deployments over time
            $deployments = JobAssignment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereNotNull('template_id')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Most used templates
            $popularTemplates = DeploymentTemplate::select('id', 'template_name')
                ->withCount(['jobAssignments as deployments_count' => function($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }])
                ->having('deployments_count', '>', 0)
                ->orderBy('deployments_count', 'desc')
                ->limit(10)
                ->get();

            // Service type distribution
            $serviceTypes = DeploymentTemplate::select('service_type')
                ->where('is_active', true)
                ->get()
                ->groupBy('service_type')
                ->map->count();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'templates_by_region' => $templatesByRegion,
                    'deployments_over_time' => $deployments,
                    'popular_templates' => $popularTemplates,
                    'service_types' => $serviceTypes,
                    'period' => $period . ' days'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics: ' . $e->getMessage()
            ], 500);
        }
    }*/

/**
 * Get cities for a specific client
 */
public function getCitiesByClient($clientId)
{
    try {
        \Log::info("Getting cities for client: " . $clientId);
        
        $cities = PosTerminal::where('client_id', $clientId)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->filter()
            ->values();

        \Log::info("Found cities: " . $cities->count());

        return response()->json($cities);

    } catch (\Exception $e) {
        \Log::error('Error loading cities: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Error loading cities: ' . $e->getMessage(),
            'client_id' => $clientId
        ], 500);
    }
}

/**
 * Get provinces for a specific client
 */
public function getProvincesByClient($clientId)
{
    try {
        \Log::info("Getting provinces for client: " . $clientId);
        
        $provinces = PosTerminal::where('client_id', $clientId)
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province')
            ->filter()
            ->values();

        \Log::info("Found provinces: " . $provinces->count());

        return response()->json($provinces);

    } catch (\Exception $e) {
        \Log::error('Error loading provinces: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Error loading provinces: ' . $e->getMessage(),
            'client_id' => $clientId
        ], 500);
    }
}

/**
 * Get areas for a specific client
 */
public function getAreasByClient($clientId)
{
    try {
        \Log::info("Getting areas for client: " . $clientId);
        
        $areas = PosTerminal::where('client_id', $clientId)
            ->whereNotNull('area')
            ->where('area', '!=', '')
            ->distinct()
            ->orderBy('area')
            ->pluck('area')
            ->filter()
            ->values();

        \Log::info("Found areas: " . $areas->count());

        return response()->json($areas);

    } catch (\Exception $e) {
        \Log::error('Error loading areas: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Error loading areas: ' . $e->getMessage(),
            'client_id' => $clientId
        ], 500);
    }
}

public function getAddressesByClient($clientId)
{
    try {
        \Log::info("Getting addresses for client: " . $clientId);
        
        $addresses = PosTerminal::where('client_id', $clientId)
            ->whereNotNull('physical_address')
            ->where('physical_address', '!=', '')
            ->distinct()
            ->orderBy('physical_address')
            ->pluck('physical_address')
            ->filter()
            ->values();

        \Log::info("Found addresses: " . $addresses->count());

        return response()->json($addresses);

    } catch (\Exception $e) {
        \Log::error('Error loading addresses: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Error loading addresses: ' . $e->getMessage(),
            'client_id' => $clientId
        ], 500);
    }
}

/**
 * Get filtered terminals - UPDATED for correct field mapping
 */
public function getFilteredTerminals(Request $request)
{
    try {
        \Log::info("Getting filtered terminals", $request->all());
        
        $clientId = $request->get('client_id');
        $groupBy = $request->get('group_by');
        $filterValue = $request->get('filter_value');

        if (!$clientId || !$groupBy || !$filterValue) {  // ← REMOVED REGION_ID CHECK
            return response()->json([
                'terminals' => [],
                'message' => 'Missing required parameters'
            ]);
        }

        $query = PosTerminal::where('client_id', $clientId)
            ->where('deployment_status', '!=', 'decommissioned');
            // ← REMOVED REGION FILTER

        // Apply grouping filter based on actual database fields
        switch ($groupBy) {
            case 'city':
                $query->where('city', $filterValue);
                break;
            case 'province':
                $query->where('province', $filterValue);
                break;
            case 'area':
                $query->where('area', $filterValue);
                break;
            case 'address':
                $query->where('physical_address', $filterValue);
                break;
        }

        $terminals = $query->select([
    'id', 'terminal_id', 'merchant_name', 'physical_address', 
    'city', 'province', 'area', 'region', 'merchant_phone'  
      ])->orderBy('terminal_id')->get();

        \Log::info("Found terminals: " . $terminals->count());

        return response()->json([
            'terminals' => $terminals,
            'total_count' => $terminals->count()
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading filtered terminals', [
            'error' => $e->getMessage(),
            'request' => $request->all()
        ]);

        return response()->json([
            'terminals' => [],
            'error' => 'Error loading terminals: ' . $e->getMessage()
        ], 500);
    }
}
// app/Http/Controllers/DeploymentPlanningController.php

public function assign(Request $request)
{
    $v = Validator::make($request->all(), [
        'technician_id'        => 'required|exists:employees,id',
        'selected_terminals'   => 'required|array|min:1',
        'selected_terminals.*' => 'integer|exists:pos_terminals,id',
        'scheduled_date'       => 'required|date|after_or_equal:today',
        'priority'             => 'nullable|in:low,normal,high,emergency',
        'service_type'         => 'nullable|string|max:100',
        'assignment_type'      => 'nullable|in:individual,team',
        'project_id'           => 'nullable|integer',
        'notes'                => 'nullable|string|max:500',
    ]);

    if ($v->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $v->errors(),
        ], 422);
    }

    DB::beginTransaction();

    try {
        $techId  = (int) $request->technician_id;
        $date    = Carbon::parse($request->scheduled_date)->toDateString();
        $termIds = array_values(array_unique($request->selected_terminals));

        // Infer client/region if all selected terminals share the same
        $terms = PosTerminal::whereIn('id', $termIds)->get(['id','client_id','region_id']);
        if ($terms->count() !== count($termIds)) {
            throw new \Exception('Some terminals were not found.');
        }
        $clientId = $terms->pluck('client_id')->unique()->count() === 1 ? $terms->first()->client_id : null;
        $regionId = $terms->pluck('region_id')->unique()->count() === 1 ? $terms->first()->region_id : null;

        // Optional: block double-assigning the SAME terminal on the SAME date
        $conflict = JobAssignment::whereDate('scheduled_date', $date)
            ->whereIn('status', ['assigned','in_progress'])
            ->where(function($q) use ($termIds) {
                foreach ($termIds as $id) {
                    $q->orWhereJsonContains('pos_terminals', $id);
                }
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'One or more terminals are already assigned for this date.',
            ], 409);
        }

        $assignment = JobAssignment::create([
            'assignment_id'            => JobAssignment::generateAssignmentId(),
            'technician_id'            => $techId,
            'region_id'                => $regionId,
            'client_id'                => $clientId,
            'project_id'               => $request->project_id,
            'scheduled_date'           => $date,
            'service_type'             => $request->service_type ?? 'routine_maintenance',
            'priority'                 => $request->priority ?? 'normal',
            'status'                   => 'assigned',
            'notes'                    => $request->notes,
            'estimated_duration_hours' => null,
            'pos_terminals'            => $termIds, // JSON array
            'created_by'               => auth()->id() ?? 1,
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'id' => $assignment->id,
            'assignment_id' => $assignment->assignment_id,
            'assigned_terminal_ids' => $termIds,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating assignment', ['error' => $e->getMessage(), 'payload' => $request->all()]);
        return response()->json([
            'success' => false,
            'message' => 'Error creating assignment: '.$e->getMessage(),
        ], 500);
    }
}

public function assignedTerminals(Request $request)
{
    // Terminals present in any "assigned" or "in_progress" jobs
    $ids = JobAssignment::whereIn('status', ['assigned','in_progress'])
        ->pluck('pos_terminals')
        ->flatten()
        ->unique()
        ->values();

    return response()->json([
        'success' => true,
        'assigned_terminal_ids' => $ids,
    ]);
}

}