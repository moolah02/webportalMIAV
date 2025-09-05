<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DeploymentController extends Controller
{
    /**
     * Display deployment planning page - For setting up regions/cities templates
     */
    public function index()
    {
        // Get all regions with their terminal counts and cities
        $regions = Region::withCount('posTerminals')
            ->with('cities')
            ->orderBy('name')
            ->get();

        // Get all clients for the import process
        $clients = Client::orderBy('company_name')->get();

        // Get technicians for job assignment (employees with technician role)
        $technicians = Employee::where(function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'Technician');
                })
                ->orWhere('role_id', 3); // Assuming role_id 3 is technician
            })
            ->select('id', 'first_name', 'last_name', 'phone', 'department_id')
            ->with('department:id,name')
            ->get()
            ->map(function($technician) {
                // Return as object, not array
                return (object)[
                    'id' => $technician->id,
                    'name' => $technician->first_name . ' ' . $technician->last_name,
                    'specialization' => $technician->department->name ?? 'General',
                    'phone' => $technician->phone
                ];
            });

        // Get deployment statistics using existing columns
        $stats = [
            'total_regions' => Region::count(),
            'active_regions' => Region::where('is_active', true)->count(),
            'total_terminals' => PosTerminal::count(),
            'assigned_terminals' => PosTerminal::whereNotNull('region_id')->count(),
            'unassigned_terminals' => PosTerminal::whereNull('region_id')->count(),
            'pending_deployment' => PosTerminal::where('current_status', 'offline')->whereNull('region_id')->count(),
            'deployed_terminals' => PosTerminal::where('current_status', 'active')->count(),
            'active_terminals' => PosTerminal::where('current_status', 'active')->count(),
        ];

        // Get recent job assignments for the assignments list (sample data for now)
        $assignments = collect([
            (object)[
                'id' => 1,
                'assignment_id' => 'ASG-0001',
                'technician' => (object)['name' => 'John Mukamuri'],
                'region' => (object)['name' => 'Harare CBD'],
                'scheduled_date' => now()->addDays(1),
                'status' => 'assigned',
                'priority' => 'high',
                'pos_terminals' => [1, 2, 3]
            ],
            (object)[
                'id' => 2,
                'assignment_id' => 'ASG-0002',
                'technician' => (object)['name' => 'Sarah Moyo'],
                'region' => (object)['name' => 'Bulawayo Central'],
                'scheduled_date' => now(),
                'status' => 'in_progress',
                'priority' => 'normal',
                'pos_terminals' => [4, 5]
            ]
        ]);

        // Assignment statistics for the metrics cards
        $todayAssignments = 2;
        $pendingAssignments = 1;
        $inProgressAssignments = 1;
        $completedToday = 0;

        return view('deployment.planning', compact(
            'regions',
            'clients',
            'stats',
            'technicians',
            'assignments',
            'todayAssignments',
            'pendingAssignments',
            'inProgressAssignments',
            'completedToday'
        ));
    }

    /**
     * Store a new region template
     */
    public function storeRegion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:regions,name',
            'region_code' => 'required|string|max:10|unique:regions,region_code',
            'description' => 'nullable|string|max:1000',
            'province' => 'nullable|string|max:255',
            'country' => 'required|string|max:255|default:Zimbabwe',
            'cities' => 'nullable|array',
            'cities.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create the region
            $region = Region::create([
                'name' => $request->name,
                'region_code' => strtoupper($request->region_code),
                'description' => $request->description,
                'province' => $request->province,
                'country' => $request->country ?? 'Zimbabwe',
                'is_active' => true,
                'created_by' => auth()->id()
            ]);

            // Add cities to the region if provided
            if ($request->cities && is_array($request->cities)) {
                foreach ($request->cities as $cityName) {
                    if (!empty(trim($cityName))) {
                        $region->cities()->create([
                            'name' => trim($cityName),
                            'is_active' => true,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Region template created successfully',
                'region' => $region->load('cities')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating region: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing region template
     */
    public function updateRegion(Request $request, $regionId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:regions,name,' . $regionId,
            'region_code' => 'required|string|max:10|unique:regions,region_code,' . $regionId,
            'description' => 'nullable|string|max:1000',
            'province' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'cities' => 'nullable|array',
            'cities.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $region = Region::findOrFail($regionId);

            // Update region details
            $region->update([
                'name' => $request->name,
                'region_code' => strtoupper($request->region_code),
                'description' => $request->description,
                'province' => $request->province,
                'country' => $request->country,
                'updated_by' => auth()->id()
            ]);

            // Update cities - remove old ones and add new ones
            if ($request->has('cities')) {
                $region->cities()->delete(); // Remove existing cities

                if (is_array($request->cities)) {
                    foreach ($request->cities as $cityName) {
                        if (!empty(trim($cityName))) {
                            $region->cities()->create([
                                'name' => trim($cityName),
                                'is_active' => true,
                                'created_by' => auth()->id()
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Region template updated successfully',
                'region' => $region->load('cities')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating region: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a region template
     */
    public function deleteRegion($regionId)
    {
        try {
            $region = Region::findOrFail($regionId);

            // Check if region has terminals
            $terminalsCount = PosTerminal::where('region_id', $regionId)->count();

            if ($terminalsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete region: {$terminalsCount} terminals are assigned to this region. Please reassign them first."
                ], 422);
            }

            // Delete cities associated with this region
            $region->cities()->delete();

            // Delete the region
            $region->delete();

            return response()->json([
                'success' => true,
                'message' => 'Region template deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting region: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get region details with terminals
     */
    public function getRegionDetails($regionId)
    {
        try {
            $region = Region::with([
                'cities',
                'posTerminals' => function($query) {
                    $query->with('client:id,company_name')
                          ->orderBy('terminal_id');
                }
            ])->findOrFail($regionId);

            return response()->json([
                'success' => true,
                'region' => $region
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading region details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk import terminals from Excel and auto-assign to regions based on city/location
     */
    public function importTerminals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:51200', // 10MB max
            'client_id' => 'required|exists:clients,id',
            'auto_assign_regions' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $clientId = $request->client_id;
            $autoAssignRegions = $request->boolean('auto_assign_regions', true);

            // Parse the Excel file
            $data = Excel::toArray([], $file)[0]; // Get first sheet

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file is empty or invalid'
                ], 422);
            }

            // Get headers from first row
            $headers = array_map('strtolower', array_map('trim', $data[0]));
            $rows = array_slice($data, 1); // Skip header row

            // Expected columns mapping
            $columnMapping = $this->getColumnMapping($headers);

            if (!$columnMapping) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Excel format. Required columns: Terminal ID, Merchant Name, City/Location'
                ], 422);
            }

            $importResults = [
                'total_rows' => count($rows),
                'successful_imports' => 0,
                'failed_imports' => 0,
                'assigned_to_regions' => 0,
                'errors' => []
            ];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Excel row number (accounting for header)

                try {
                    // Extract data from row
                    $terminalData = $this->extractTerminalData($row, $columnMapping, $clientId);

                    if (!$terminalData) {
                        $importResults['failed_imports']++;
                        $importResults['errors'][] = "Row {$rowNumber}: Missing required data";
                        continue;
                    }

                    // Auto-assign region based on city if enabled
                    if ($autoAssignRegions && !empty($terminalData['city'])) {
                        $regionId = $this->findRegionByCity($terminalData['city']);
                        if ($regionId) {
                            $terminalData['region_id'] = $regionId;
                            $terminalData['current_status'] = 'active'; // Assigned terminals are active
                            $importResults['assigned_to_regions']++;
                        }
                    }

                    // Create or update terminal
                    PosTerminal::updateOrCreate(
                        ['terminal_id' => $terminalData['terminal_id']],
                        $terminalData
                    );

                    $importResults['successful_imports']++;

                } catch (\Exception $e) {
                    $importResults['failed_imports']++;
                    $importResults['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import completed successfully',
                'results' => $importResults
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map Excel column headers to our database fields
     */
    private function getColumnMapping($headers)
    {
        $mapping = [];

        // Define possible column name variations
        $columnVariations = [
            'terminal_id' => ['terminal_id', 'terminal id', 'id', 'terminal', 'pos_id'],
            'merchant_name' => ['merchant_name', 'merchant name', 'merchant', 'business_name', 'shop_name'],
            'city' => ['city', 'location', 'town', 'area', 'address'],
            'contact_person' => ['contact_person', 'contact person', 'contact', 'manager'],
            'phone' => ['phone', 'telephone', 'mobile', 'contact_number'],
            'address' => ['address', 'physical_address', 'street_address', 'location_details']
        ];

        foreach ($columnVariations as $field => $variations) {
            foreach ($variations as $variation) {
                if (($key = array_search($variation, $headers)) !== false) {
                    $mapping[$field] = $key;
                    break;
                }
            }
        }

        // Check if we have minimum required fields
        return (isset($mapping['terminal_id']) && isset($mapping['merchant_name'])) ? $mapping : false;
    }

    /**
     * Extract terminal data from Excel row
     */
    private function extractTerminalData($row, $mapping, $clientId)
    {
        $terminalId = trim($row[$mapping['terminal_id']] ?? '');
        $merchantName = trim($row[$mapping['merchant_name']] ?? '');

        if (empty($terminalId) || empty($merchantName)) {
            return false;
        }

        return [
            'terminal_id' => $terminalId,
            'client_id' => $clientId,
            'merchant_name' => $merchantName,
            'city' => trim($row[$mapping['city']] ?? ''),
            'merchant_contact_person' => trim($row[$mapping['contact_person']] ?? ''),
            'merchant_phone' => trim($row[$mapping['phone']] ?? ''),
            'physical_address' => trim($row[$mapping['address']] ?? ''),
            'current_status' => 'offline', // Use offline for newly imported terminals
            'status' => 'offline', // Also set the status field
            'last_updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * Find region by city name
     */
    private function findRegionByCity($cityName)
    {
        $cityName = strtolower(trim($cityName));

        // First try exact match with cities
        $city = DB::table('cities')
            ->whereRaw('LOWER(name) = ?', [$cityName])
            ->where('is_active', true)
            ->first();

        if ($city) {
            return $city->region_id;
        }

        // Then try partial match with region names
        $region = Region::whereRaw('LOWER(name) LIKE ?', ["%{$cityName}%"])
            ->where('is_active', true)
            ->first();

        return $region ? $region->id : null;
    }

    /**
     * Get deployment statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_regions' => Region::count(),
                'active_regions' => Region::where('is_active', true)->count(),
                'total_cities' => DB::table('cities')->count(),
                'total_terminals' => PosTerminal::count(),
                'assigned_terminals' => PosTerminal::whereNotNull('region_id')->count(),
                'unassigned_terminals' => PosTerminal::whereNull('region_id')->count(),
                'pending_deployment' => PosTerminal::where('current_status', 'offline')->whereNull('region_id')->count(),
                'deployed_terminals' => PosTerminal::where('current_status', 'active')->count(),
                'active_terminals' => PosTerminal::where('current_status', 'active')->count(),
                'maintenance_terminals' => PosTerminal::where('current_status', 'maintenance')->count(),
                'faulty_terminals' => PosTerminal::where('current_status', 'faulty')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unassigned terminals for manual assignment
     */
    public function getUnassignedTerminals()
    {
        try {
            $terminals = PosTerminal::whereNull('region_id')
                ->with('client:id,company_name')
                ->orderBy('terminal_id')
                ->get();

            return response()->json([
                'success' => true,
                'terminals' => $terminals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading terminals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually assign terminals to regions
     */
    public function assignTerminalsToRegion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'required|exists:regions,id',
            'terminal_ids' => 'required|array',
            'terminal_ids.*' => 'exists:pos_terminals,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $assigned = PosTerminal::whereIn('id', $request->terminal_ids)
                ->update([
                    'region_id' => $request->region_id,
                    'current_status' => 'active', // Mark as active when assigned
                    'status' => 'active',
                    'last_updated_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$assigned} terminals assigned to region successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning terminals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get terminals by region for deployment planning
     */
    public function getTerminalsByRegion($regionId)
    {
        try {
            $terminals = PosTerminal::where('region_id', $regionId)
                ->with('client:id,company_name')
                ->orderBy('terminal_id')
                ->get();

            return response()->json([
                'success' => true,
                'terminals' => $terminals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading terminals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update terminal deployment status
     */
    public function updateTerminalStatus(Request $request, $terminalId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,offline,maintenance,faulty,decommissioned',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $terminal = PosTerminal::findOrFail($terminalId);

            $terminal->update([
                'current_status' => $request->status,
                'status' => $request->status,
                'last_updated_by' => auth()->id(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terminal status updated successfully',
                'terminal' => $terminal->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating terminal status: ' . $e->getMessage()
            ], 500);
        }
    }
}
