<?php

namespace App\Http\Controllers;

use App\Models\PosTerminal;
use App\Models\Client;
use App\Models\Region;
use App\Models\ImportMapping;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PosTerminalController extends Controller
{
   

    public function create()
    {
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();
        
        // Get options - handle if Category model doesn't exist
        $statusOptions = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);
        
        try {
            if (class_exists('App\Models\Category')) {
                $statusOptions = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.create', compact(
            'clients', 
            'regions', 
            'statusOptions', 
            'serviceTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id',
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string',
            'merchant_contact_person' => 'nullable|string',
            'merchant_phone' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'business_type' => 'nullable|string',
            'terminal_model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'contract_details' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Set default status if not provided
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['current_status'] = $validated['status']; // Sync both status fields

        PosTerminal::create($validated);

        return redirect()->route('pos-terminals.index')
                        ->with('success', 'POS Terminal created successfully.');
    }


    public function show(PosTerminal $posTerminal)
    {
        // Load only existing relationships - remove problematic ones for now
        $posTerminal->load(['client']);
        
        // Try to load other relationships if they exist, but don't fail if they don't
        try {
            if (method_exists($posTerminal, 'regionModel')) {
                $posTerminal->load(['regionModel']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load regionModel: ' . $e->getMessage());
        }
        
        try {
            if (method_exists($posTerminal, 'jobAssignments')) {
                $posTerminal->load(['jobAssignments']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load jobAssignments: ' . $e->getMessage());
        }
        
        try {
            if (method_exists($posTerminal, 'tickets')) {
                $posTerminal->load(['tickets']);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not load tickets: ' . $e->getMessage());
        }
        
        // Get related categories for display - handle if Category model doesn't exist
        $statusCategories = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);
        
        try {
            if (class_exists('App\Models\Category')) {
                $statusCategories = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            \Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.show', compact(
            'posTerminal', 
            'statusCategories', 
            'serviceTypes'
        ));
    }

    public function edit(PosTerminal $posTerminal)
    {
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();
        
        // Get options - handle if Category model doesn't exist
        $statusOptions = collect(['active', 'offline', 'faulty', 'maintenance']);
        $serviceTypes = collect(['maintenance', 'installation', 'repair']);
        
        try {
            if (class_exists('App\Models\Category')) {
                $statusOptions = Category::getTerminalStatuses();
                $serviceTypes = Category::getServiceTypes();
            }
        } catch (\Exception $e) {
            Log::warning('Category model not available: ' . $e->getMessage());
        }

        return view('pos-terminals.edit', compact(
            'posTerminal', 
            'clients', 
            'regions', 
            'statusOptions', 
            'serviceTypes'
        ));
    }

    public function update(Request $request, PosTerminal $posTerminal)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string|unique:pos_terminals,terminal_id,' . $posTerminal->id,
            'client_id' => 'required|exists:clients,id',
            'merchant_name' => 'required|string',
            'merchant_contact_person' => 'nullable|string',
            'merchant_phone' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'physical_address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'business_type' => 'nullable|string',
            'terminal_model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'installation_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_due' => 'nullable|date',
            'contract_details' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Sync both status fields
        if (isset($validated['status'])) {
            $validated['current_status'] = $validated['status'];
        }

        $posTerminal->update($validated);

        return redirect()->route('pos-terminals.show', $posTerminal)
                        ->with('success', 'POS Terminal updated successfully.');
    }

    public function updateStatus(Request $request, PosTerminal $posTerminal)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $posTerminal->update([
            'status' => $request->status,
            'current_status' => $request->status,
        ]);

        return back()->with('success', 'Terminal status updated successfully.');
    }

    public function destroy(PosTerminal $posTerminal)
    {
        $posTerminal->delete();

        return redirect()->route('pos-terminals.index')
                        ->with('success', 'POS Terminal deleted successfully.');
    }
    /**
     * Delete a column mapping
     */
    public function deleteColumnMapping(ImportMapping $mapping)
    {
        try {
            $mapping->delete();
            return redirect()->back()->with('success', 'Column mapping deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting column mapping: ' . $e->getMessage());
        }
    }

    /**
     * Toggle column mapping active status
     */
    public function toggleColumnMapping(ImportMapping $mapping)
    {
        try {
            $mapping->update(['is_active' => !$mapping->is_active]);
            $status = $mapping->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Column mapping {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Error toggling column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating column mapping: ' . $e->getMessage());
        }
    }

    /**
     * Get column mapping data for AJAX requests
     */
    public function getColumnMapping(ImportMapping $mapping)
    {
        try {
            return response()->json([
                'success' => true,
                'mapping' => $mapping
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading column mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * Export terminals to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = PosTerminal::with(['client']);

            // Apply same filters as index method
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('terminal_id', 'like', "%{$search}%")
                      ->orWhere('merchant_name', 'like', "%{$search}%")
                      ->orWhere('merchant_contact_person', 'like', "%{$search}%");
                });
            }

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

            if ($request->filled('province')) {
                $query->where('province', $request->province);
            }

            $terminals = $query->get();

            $filename = 'pos_terminals_export_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $handle = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($handle, [
                'Terminal ID',
                'Client',
                'Merchant Name',
                'Contact Person',
                'Phone',
                'Email',
                'Address',
                'City',
                'Province',
                'Region',
                'Business Type',
                'Terminal Model',
                'Serial Number',
                'Status',
                'Installation Date',
                'Last Service Date',
                'Next Service Due',
                'Contract Details'
            ]);

            // Export data
            foreach ($terminals as $terminal) {
                fputcsv($handle, [
                    $terminal->terminal_id,
                    $terminal->client->company_name ?? '',
                    $terminal->merchant_name,
                    $terminal->merchant_contact_person,
                    $terminal->merchant_phone,
                    $terminal->merchant_email,
                    $terminal->physical_address,
                    $terminal->city,
                    $terminal->province,
                    $terminal->region,
                    $terminal->business_type,
                    $terminal->terminal_model,
                    $terminal->serial_number,
                    $terminal->status,
                    $terminal->installation_date,
                    $terminal->last_service_date,
                    $terminal->next_service_due,
                    $terminal->contract_details
                ]);
            }
            
            fclose($handle);
            exit;

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

     
    public function showImport()
    {
        $clients = Client::orderBy('company_name')->get();
        
        // Get available column mappings
        $mappings = collect(); // Empty collection for now
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }
        
        return view('pos-terminals.import', compact('clients', 'mappings'));
    }

    /**
     * Show column mapping management page
     */
    public function showColumnMapping()
    {
        $clients = Client::orderBy('company_name')->get();
        
        $mappings = collect(); // Empty collection for now
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::with('client')->where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }
        
        return view('pos-terminals.column-mapping', compact('clients', 'mappings'));
    }

    /**
     * Store new column mapping
     */
    public function storeColumnMapping(Request $request)
    {
        $validated = $request->validate([
            'mapping_name' => 'required|string|unique:import_mappings,mapping_name',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'column_mappings' => 'required|array',
            'column_mappings.*' => 'nullable|integer|min:0|max:50'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;
        
        try {
            if (class_exists('App\Models\ImportMapping')) {
                ImportMapping::create($validated);
                return redirect()->back()->with('success', 'Column mapping saved successfully!');
            } else {
                return redirect()->back()->with('error', 'ImportMapping model not available. Please create the model first.');
            }
        } catch (\Exception $e) {
            Log::error('Error saving column mapping: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error saving column mapping: ' . $e->getMessage());
        }
    }
    /**
     * Process CSV with dynamic column mapping
     */
    private function processCSVImportWithMapping($filePath, int $clientId, array $options, ?ImportMapping $mapping)
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $skipDuplicates = in_array('skip_duplicates', $options);
        $updateExisting = in_array('update_existing', $options);

        // Read file with proper encoding detection
        $fileContent = file_get_contents($filePath);
        
        $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
            Log::info("Converted file from {$encoding} to UTF-8");
        }

        $lines = str_getcsv($fileContent, "\n");
        $rowNumber = 0;
        $headers = null;

        foreach ($lines as $line) {
            $rowNumber++;
            
            $row = str_getcsv($line, ",");
            
            if ($rowNumber === 1) {
                $headers = $row;
                Log::info('Headers: ' . json_encode($headers));
                continue;
            }

            if (empty(array_filter($row)) || count($row) < 2) {
                continue;
            }

            try {
                // Use dynamic mapping or default mapping
                $terminalData = $mapping 
                    ? $this->mapRowDataDynamic($row, $clientId, $mapping, $rowNumber)
                    : $this->mapRowData($row, $clientId, $rowNumber);
                
                Log::info("Row {$rowNumber} mapped data: " . json_encode($terminalData));

                // Validate required fields
                if (empty($terminalData['terminal_id'])) {
                    $results['errors'][] = "Row {$rowNumber}: Terminal ID is empty";
                    continue;
                }

                if (empty($terminalData['merchant_name'])) {
                    $results['errors'][] = "Row {$rowNumber}: Merchant name is empty";
                    continue;
                }

                // Check if terminal exists and handle accordingly
                $existingTerminal = PosTerminal::where('terminal_id', $terminalData['terminal_id'])->first();

                if ($existingTerminal) {
                    if ($skipDuplicates && !$updateExisting) {
                        $results['skipped']++;
                        continue;
                    }

                    if ($updateExisting) {
                        $existingTerminal->update($terminalData);
                        $results['updated']++;
                    } else {
                        $results['errors'][] = "Row {$rowNumber}: Terminal ID '{$terminalData['terminal_id']}' already exists";
                    }
                } else {
                    PosTerminal::create($terminalData);
                    $results['created']++;
                }

            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("Row {$rowNumber} error: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Map CSV row data to terminal fields - FIXED FOR YOUR EXACT CSV FORMAT
     */
    private function mapRowData(array $row, int $clientId, int $rowNumber = 0)
    {
        // Pad array to ensure we have enough elements (21 columns: A-U)
        $row = array_pad($row, 21, '');
        
        // Log raw row data for debugging
        if ($rowNumber > 0) {
            Log::info("Row {$rowNumber} raw data: " . json_encode(array_slice($row, 0, 10)));
        }
        
        // Clean and trim all values
        $row = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $row);

        $terminalData = [
            // STATIC DATA (Bank Import - Grey/Pink in Excel)
            'client_id' => $clientId,
            'terminal_id' => $this->cleanValue($row[1]), // Column B: Terminal ID
            'business_type' => $this->cleanValue($row[2]) ?: null, // Column C: Type(from bank)
            'merchant_name' => $this->cleanValue($row[4]), // Column E: Client Full Name
            'physical_address' => $this->cleanValue($row[5]) ?: null, // Column F: Address
            'city' => $this->cleanValue($row[6]) ?: null, // Column G: City
            'province' => $this->cleanValue($row[7]) ?: null, // Column H: Province
            'merchant_phone' => $this->cleanPhoneNumber($row[8]) ?: null, // Column I: Phone Number(from Bank)
            'region' => $this->cleanValue($row[9]) ?: null, // Column J: REGION
            
            // DYNAMIC DATA (Technician Updates - Blue/Green in Excel)
            'installation_date' => $this->parseDate($row[10]) ?: null, // Column K: Date
            'merchant_contact_person' => $this->cleanValue($row[11]) ?: null, // Column L: Teams
            'terminal_model' => $this->cleanValue($row[12]) ?: null, // Column M: Device Type
            'serial_number' => $this->cleanValue($row[13]) ?: null, // Column N: Serial Number
            'status' => $this->mapStatus($row[14]) ?: 'active', // Column O: Status
            'current_status' => $this->mapStatus($row[14]) ?: 'active', // Sync both status fields
            
            // Build comprehensive contract details from remaining columns
            'contract_details' => $this->buildContractDetails($row),
            
            // Set default values for fields not in Excel
            'merchant_email' => null,
            'last_service_date' => null,
            'next_service_due' => null,
        ];

        return $terminalData;
    }

    /**
     * Map row data using dynamic column mapping
     */
    private function mapRowDataDynamic(array $row, int $clientId, ImportMapping $mapping, int $rowNumber = 0)
    {
        $row = array_pad($row, 50, ''); // Pad to 50 columns for safety
        
        Log::info("Row {$rowNumber} using mapping: " . $mapping->mapping_name);
        
        $columnMappings = $mapping->column_mappings;
        
        $terminalData = [
            'client_id' => $clientId,
            
            // Use dynamic column mappings
            'terminal_id' => $this->getValueFromMapping($row, $columnMappings, 'terminal_id'),
            'merchant_name' => $this->getValueFromMapping($row, $columnMappings, 'merchant_name'),
            'business_type' => $this->getValueFromMapping($row, $columnMappings, 'business_type'),
            'physical_address' => $this->getValueFromMapping($row, $columnMappings, 'physical_address'),
            'city' => $this->getValueFromMapping($row, $columnMappings, 'city'),
            'province' => $this->getValueFromMapping($row, $columnMappings, 'province'),
            'region' => $this->getValueFromMapping($row, $columnMappings, 'region'),
            'merchant_phone' => $this->cleanPhoneNumber($this->getValueFromMapping($row, $columnMappings, 'merchant_phone')),
            'merchant_contact_person' => $this->getValueFromMapping($row, $columnMappings, 'merchant_contact_person'),
            'terminal_model' => $this->getValueFromMapping($row, $columnMappings, 'terminal_model'),
            'serial_number' => $this->getValueFromMapping($row, $columnMappings, 'serial_number'),
            'installation_date' => $this->parseDate($this->getValueFromMapping($row, $columnMappings, 'installation_date')),
            'status' => $this->mapStatus($this->getValueFromMapping($row, $columnMappings, 'status')) ?: 'active',
            'current_status' => $this->mapStatus($this->getValueFromMapping($row, $columnMappings, 'status')) ?: 'active',
            
            // Build contract details from comments, issues, etc.
            'contract_details' => $this->buildDynamicContractDetails($row, $columnMappings),
            
            // Defaults
            'merchant_email' => null,
            'last_service_date' => null,
            'next_service_due' => null,
        ];

        // Remove null values
        return array_filter($terminalData, function($value) {
            return $value !== null && $value !== '';
        });
    }

    /**
     * Get value from row using column mapping
     */
    private function getValueFromMapping(array $row, array $mappings, string $field): ?string
    {
        $columnIndex = $mappings[$field] ?? null;
        
        if ($columnIndex === null || !isset($row[$columnIndex])) {
            return null;
        }
        
        return $this->cleanValue($row[$columnIndex]);
    }

    /**
     * Build contract details using dynamic mapping
     */
    private function buildDynamicContractDetails(array $row, array $mappings): ?string
    {
        $details = [];
        
        $commentFields = ['condition', 'issues', 'comments', 'corrective_action', 'site_contact', 'site_phone'];
        
        foreach ($commentFields as $field) {
            $value = $this->getValueFromMapping($row, $mappings, $field);
            if (!empty($value)) {
                $details[] = ucfirst(str_replace('_', ' ', $field)) . ": " . $value;
            }
        }
        
        return !empty($details) ? implode("\n", $details) : null;
    }

    private function buildContractDetails(array $row): ?string
    {
        $details = [];
        
        // Column P: Condition
        if (!empty(trim($row[15] ?? ''))) {
            $details[] = "Condition: " . trim($row[15]);
        }
        
        // Column Q: Issue Raised
        if (!empty(trim($row[16] ?? ''))) {
            $details[] = "Issues: " . trim($row[16]);
        }
        
        // Column R: Comments
        if (!empty(trim($row[17] ?? ''))) {
            $details[] = "Comments: " . trim($row[17]);
        }
        
        // Column S: Corrective Action
        if (!empty(trim($row[18] ?? ''))) {
            $details[] = "Corrective Action: " . trim($row[18]);
        }
        
        // Column T: Contact Person
        if (!empty(trim($row[19] ?? ''))) {
            $details[] = "Site Contact: " . trim($row[19]);
        }
        
        // Column U: Contact Number
        if (!empty(trim($row[20] ?? ''))) {
            $details[] = "Site Phone: " . trim($row[20]);
        }
        
        return !empty($details) ? implode("\n", $details) : null;
    }

public function index(Request $request)
{
    $query = PosTerminal::with(['client']);

    // Apply filters with proper column qualification
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
        // ENSURE we only accept valid status values
        $validStatuses = ['active', 'offline', 'faulty', 'maintenance'];
        $status = $request->status;
        
        if (in_array($status, $validStatuses)) {
            $query->where('pos_terminals.status', $status);
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

    // FIXED: Calculate filtered statistics BEFORE pagination
    $stats = $this->calculateFilteredStats(clone $query);

    // Then paginate
    $terminals = $query->paginate(20);

    // Get filter options
    $clients = Client::orderBy('company_name')->get();
    $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();
    $cities = PosTerminal::distinct()->pluck('city')->filter()->sort();
    $provinces = PosTerminal::distinct()->pluck('province')->filter()->sort();

    // FIXED: Proper status options format
    $statusOptions = [
        'active' => 'Active',
        'offline' => 'Offline', 
        'faulty' => 'Faulty',
        'maintenance' => 'Maintenance'
    ];

    // Get mappings for import tab
    $mappings = collect();
    try {
        if (class_exists('App\Models\ImportMapping')) {
            $mappings = ImportMapping::where('is_active', true)->get();
        }
    } catch (\Exception $e) {
        Log::warning('ImportMapping model not available: ' . $e->getMessage());
    }

    // Handle AJAX requests
    if ($request->ajax() || $request->has('ajax')) {
        return $this->handleAjaxRequest($request, clone $query, $stats);
    }

    return view('pos-terminals.index', compact(
        'terminals', 
        'clients', 
        'regions', 
        'cities', 
        'provinces', 
        'statusOptions',
        'mappings',
        'stats'
    ));
}


/**
 * Handle AJAX requests for filtered data
 */
private function handleAjaxRequest(Request $request, $query, $stats)
{
    try {
        // Get filtered terminals for charts
        $terminals = $query->get();

        // Generate chart data
        $chartData = $this->generateChartData($terminals);

        // Generate table HTML
        $tableHtml = $this->generateTableHtml($terminals, $request);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chartData' => $chartData,
            'tableHtml' => $tableHtml
        ]);

    } catch (\Exception $e) {
        Log::error('AJAX request error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading filtered data'
        ], 500);
    }
}

/**
 * Generate chart data from filtered terminals
 */
private function generateChartData($terminals)
{
    // Status distribution
    $statusCounts = $terminals->countBy('status');
    
    // Location distribution (top 10)
    $locationCounts = $terminals->countBy(function($terminal) {
        return $terminal->region ?: 'No Region';
    })->sortDesc()->take(10);

    return [
        'status' => [
            'active' => $statusCounts['active'] ?? 0,
            'offline' => $statusCounts['offline'] ?? 0,
            'faulty' => ($statusCounts['faulty'] ?? 0) + ($statusCounts['maintenance'] ?? 0)
        ],
        'locations' => [
            'labels' => $locationCounts->keys()->toArray(),
            'data' => $locationCounts->values()->toArray()
        ]
    ];
}

/**
 * Generate table HTML for filtered results
 */
private function generateTableHtml($terminals, Request $request)
{
    // Paginate the terminals
    $currentPage = $request->get('page', 1);
    $perPage = 20;
    $paginatedTerminals = new \Illuminate\Pagination\LengthAwarePaginator(
        $terminals->forPage($currentPage, $perPage),
        $terminals->count(),
        $perPage,
        $currentPage,
        [
            'path' => $request->url(),
            'pageName' => 'page',
        ]
    );
    $paginatedTerminals->appends($request->query());

    return view('pos-terminals.partials.table', ['terminals' => $paginatedTerminals])->render();
}

/**
 * Get filtered statistics endpoint
 */
public function getFilteredStats(Request $request)
{
    $query = PosTerminal::query();

    // Apply same filters as index method
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('terminal_id', 'like', "%{$search}%")
              ->orWhere('merchant_name', 'like', "%{$search}%")
              ->orWhere('merchant_contact_person', 'like', "%{$search}%");
        });
    }

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

    if ($request->filled('province')) {
        $query->where('province', $request->province);
    }

    $stats = $this->calculateFilteredStats($query);
    $chartData = $this->generateChartData($query->get());

    return response()->json([
        'success' => true,
        'stats' => $stats,
        'chartData' => $chartData
    ]);
}


public function createTicket(Request $request, PosTerminal $posTerminal)
{
    $validated = $request->validate([
        'priority' => 'required|in:low,medium,high,critical',
        'issue_type' => 'required|string',
        'description' => 'required|string',
        'reported_by' => 'nullable|string',
        'contact_number' => 'nullable|string'
    ]);
    
    // Create ticket logic here
    // $ticket = $posTerminal->tickets()->create($validated);
    
    return response()->json(['success' => true, 'message' => 'Ticket created successfully']);
}

public function scheduleService(Request $request, PosTerminal $posTerminal)
{
    $validated = $request->validate([
        'service_type' => 'required|string',
        'scheduled_date' => 'required|date|after:today',
        'scheduled_time' => 'required',
        'technician_id' => 'nullable|exists:users,id',
        'notes' => 'nullable|string'
    ]);
    
    if ($request->update_next_service) {
        $posTerminal->update([
            'next_service_due' => $validated['scheduled_date']
        ]);
    }
    
    // Create service appointment logic
    
    return response()->json(['success' => true, 'message' => 'Service scheduled successfully']);
}

public function addNote(Request $request, PosTerminal $posTerminal)
{
    $validated = $request->validate([
        'note_type' => 'required|string',
        'notes' => 'required|string',
        'is_important' => 'boolean'
    ]);
    
    // Add note logic here
    
    return response()->json(['success' => true, 'message' => 'Note added successfully']);
}

/*public function generateReport(Request $request, PosTerminal $posTerminal, $type)
{
    // Report generation logic based on type
    $format = $request->input('format', 'pdf');
    
    // Generate report based on type and format
    
    return response()->download($reportPath);
}*/

public function getStatistics(PosTerminal $posTerminal)
{
    return response()->json([
        'total_jobs' => $posTerminal->jobs()->count(),
        'service_reports' => $posTerminal->serviceReports()->count(),
        'open_tickets' => $posTerminal->tickets()->where('status', 'open')->count(),
        'days_since_service' => $posTerminal->last_service_date 
            ? $posTerminal->last_service_date->diffInDays(now()) 
            : null
    ]);
}
/**
 * Calculate comprehensive statistics for all chart types
 */
/**
 * Calculate comprehensive statistics - FIXED for column ambiguity
 */
/**
 * Calculate statistics for filtered results - COMPLETE FIX
 */
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
/**
 * Get chart data for AJAX requests
 */
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
private function parseCSVContent($filePath)
{
    try {
        // Read file content
        $fileContent = file_get_contents($filePath);
        
        if ($fileContent === false) {
            throw new \Exception('Unable to read file content');
        }

        // Handle encoding
        $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
        }

        // Split into lines properly handling different line endings
        $lines = preg_split('/\r\n|\r|\n/', $fileContent);
        
        $csvData = [];
        foreach ($lines as $lineIndex => $line) {
            // Skip empty lines
            if (trim($line) === '') {
                continue;
            }
            
            // Parse CSV line
            $row = str_getcsv($line, ",");
            
            // Clean each value
            $row = array_map(function($value) {
                $cleaned = trim($value);
                return $cleaned === '' ? null : $cleaned;
            }, $row);
            
            $csvData[] = $row;
        }
        
        if (empty($csvData)) {
            throw new \Exception('No data found in CSV file');
        }
        
        Log::info("Parsed CSV: " . count($csvData) . " rows (including headers)");
        
        return $csvData;
        
    } catch (\Exception $e) {
        Log::error('Error parsing CSV content: ' . $e->getMessage());
        throw $e;
    }
}
public function import(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'client_id' => 'required|exists:clients,id',
            'mapping_id' => 'nullable|exists:import_mappings,id',
            'options' => 'nullable|array'
        ]);

        $file = $request->file('file');
        $clientId = $request->client_id;
        $mappingId = $request->mapping_id;
        $options = $request->options ?? [];

        Log::info('Starting import process', [
            'client_id' => $clientId,
            'mapping_id' => $mappingId,
            'options' => $options,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize()
        ]);

        // Get column mapping if specified
        $mapping = null;
        if ($mappingId && class_exists('App\Models\ImportMapping')) {
            $mapping = ImportMapping::find($mappingId);
        }

        // Process the CSV file
        $results = $this->processCSVImportEnhanced($file->getPathname(), $clientId, $options, $mapping);

        // Log results
        Log::info('Import completed', $results);

        // Prepare success message
        return redirect()->route('pos-terminals.index')
                         ->with('success', 'Import completed successfully! ' . json_encode($results));
    } catch (\Exception $e) {
        Log::error('Import failed: ' . $e->getMessage());
        return back()->with('error', 'Import failed: ' . $e->getMessage())->withInput();
    }
}

private function processCSVImportEnhanced($filePath, int $clientId, array $options, ?ImportMapping $mapping = null)
{
    $results = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => []
    ];

    try {
        // Read and parse CSV file
        $fileContent = file_get_contents($filePath);
        
        // Handle encoding
        $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
            Log::info("Converted file from {$encoding} to UTF-8");
        }

        // Split into lines and parse
        $lines = preg_split('/\r\n|\r|\n/', $fileContent);
        $rowNumber = 0;
        $headers = null;

        foreach ($lines as $line) {
            $rowNumber++;
            
            // Skip empty lines
            if (trim($line) === '') {
                continue;
            }

            $row = str_getcsv($line, ",");
            
            // Handle headers
            if ($headers === null) {
                $headers = $row;
                Log::info('CSV Headers detected: ' . json_encode($headers));
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($row))) {
                Log::info("Skipping empty row {$rowNumber}");
                continue;
            }

            try {
                // Map the row data based on mapping or default
                $terminalData = $mapping 
                    ? $this->mapRowDataDynamic($row, $clientId, $mapping, $rowNumber)
                    : $this->mapRowDataFixed($row, $clientId, $rowNumber);
                
                Log::info("Row {$rowNumber} mapped data: " . json_encode($terminalData));

                // Check for existing terminal
                $existingTerminal = PosTerminal::where('terminal_id', $terminalData['terminal_id'])->first();

                if ($existingTerminal) {
                    if (in_array('skip_duplicates', $options)) {
                        $results['skipped']++;
                        Log::info("Skipped duplicate: {$terminalData['terminal_id']}");
                    } else {
                        $existingTerminal->update($terminalData);
                        $results['updated']++;
                        Log::info("Updated terminal: {$terminalData['terminal_id']}");
                    }
                } else {
                    PosTerminal::create($terminalData);
                    $results['created']++;
                    Log::info("Created terminal: {$terminalData['terminal_id']}");
                }

            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("Row {$rowNumber} error: " . $e->getMessage());
            }
        }

        Log::info("Import processing completed", $results);

    } catch (\Exception $e) {
        Log::error('CSV file processing error: ' . $e->getMessage());
        $results['errors'][] = 'File processing error: ' . $e->getMessage();
    }

    return $results;
}

private function mapRowDataFixed(array $row, int $clientId, int $rowNumber = 0)
{
    // Ensure we have enough columns
    $row = array_pad($row, 21, null);
    
    Log::info("Row {$rowNumber} raw data (first 10): " . json_encode(array_slice($row, 0, 10)));
    
    $terminalData = [
        'client_id' => $clientId,
        
        // Core required fields
        'terminal_id' => $this->cleanValue($row[1]), // Column B
        'merchant_name' => $this->cleanValue($row[4]), // Column E: Client Full Name
        
        // Business information
        'business_type' => $this->cleanValue($row[2]), // Column C: Type(from bank)
        'physical_address' => $this->cleanValue($row[5]), // Column F: Address
        'city' => $this->cleanValue($row[6]), // Column G: City
        'province' => $this->cleanValue($row[7]), // Column H: Province
        'region' => $this->cleanValue($row[9]), // Column J: REGION
        
        // Contact information
        'merchant_phone' => $this->cleanPhoneNumber($row[8]), // Column I: Phone Number
        'merchant_contact_person' => $this->cleanValue($row[19]), // Column T: Contact Person
        
        // Technical details
        'terminal_model' => $this->cleanValue($row[12]), // Column M: Device Type
        'serial_number' => $this->cleanValue($row[13]), // Column N: Serial Number
        
        // Dates
        'installation_date' => $this->parseDate($row[10]), // Column K: Date
        
        // Status
        'status' => $this->mapStatus($row[14]) ?: 'active', // Column O: Status
        'current_status' => $this->mapStatus($row[14]) ?: 'active',
        
        // Contract details from multiple columns
        'contract_details' => $this->buildContractDetailsFixed($row),
    ];

    // Remove empty values to use database defaults
    return array_filter($terminalData, function($value) {
        return $value !== null && $value !== '';
    });
}

/**
 * Enhanced terminal data validation
 */
private function validateTerminalData(array $terminalData, int $rowNumber)
{
    // Check required fields
    if (empty($terminalData['terminal_id'])) {
        return [
            'valid' => false,
            'error' => "Row {$rowNumber}: Terminal ID is required and cannot be empty"
        ];
    }

    if (empty($terminalData['merchant_name'])) {
        return [
            'valid' => false,
            'error' => "Row {$rowNumber}: Merchant name is required and cannot be empty"
        ];
    }

    // Validate terminal ID format (basic check)
    if (strlen($terminalData['terminal_id']) < 3) {
        return [
            'valid' => false,
            'error' => "Row {$rowNumber}: Terminal ID '{$terminalData['terminal_id']}' is too short"
        ];
    }

    // Validate email if provided
    if (!empty($terminalData['merchant_email']) && !filter_var($terminalData['merchant_email'], FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'error' => "Row {$rowNumber}: Invalid email format '{$terminalData['merchant_email']}'"
        ];
    }

    return ['valid' => true];
}

/**
 * Fixed contract details builder
 */
private function buildContractDetailsFixed(array $row): ?string
{
    $details = [];
    
    // Map according to your template structure
    $fields = [
        15 => 'Condition',        // Column P
        16 => 'Issues',           // Column Q
        17 => 'Comments',         // Column R
        18 => 'Corrective Action', // Column S
        20 => 'Site Phone'        // Column U: Contact Number
    ];
    
    foreach ($fields as $index => $label) {
        $value = $this->cleanValue($row[$index] ?? null);
        if (!empty($value)) {
            $details[] = "{$label}: {$value}";
        }
    }
    
    return !empty($details) ? implode("\n", $details) : null;
}

/**
 * Enhanced value cleaning with null handling
 */
private function cleanValue($value)
{
    if (is_null($value)) {
        return null;
    }
    
    $cleaned = trim((string)$value);
    
    // Handle various null representations
    $nullValues = ['null', 'n/a', 'na', '', '-', 'nil', 'none', 'empty'];
    if (in_array(strtolower($cleaned), $nullValues)) {
        return null;
    }
    
    return $cleaned === '' ? null : $cleaned;
}

/**
 * Enhanced date parsing with more formats
 */
private function parseDate($dateValue)
{
    if (empty($dateValue) || is_null($dateValue)) {
        return null;
    }

    $dateValue = trim((string)$dateValue);
    
    // Handle null representations
    if (in_array(strtolower($dateValue), ['null', 'n/a', 'na', '', '-'])) {
        return null;
    }

    try {
        $formats = [
            'Y-m-d',        // 2024-01-15
            'd/m/Y',        // 15/01/2024
            'm/d/Y',        // 01/15/2024
            'd-m-Y',        // 15-01-2024
            'm-d-Y',        // 01-15-2024
            'd-M-Y',        // 15-Jan-2024
            'd-M-y',        // 15-Jan-24
            'd-M',          // 19-Apr
            'j-M',          // 19-Apr (single digit day)
            'M-d',          // Apr-19
            'Y/m/d',        // 2024/01/15
            'd.m.Y',        // 15.01.2024
        ];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateValue);
            if ($date !== false) {
                // For formats without year, assume current year
                if (strpos($format, 'Y') === false && strpos($format, 'y') === false) {
                    $date->setDate(date('Y'), $date->format('n'), $date->format('j'));
                }
                return $date->format('Y-m-d');
            }
        }
        
        // Try Carbon/strtotime as fallback
        $timestamp = strtotime($dateValue);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        Log::warning("Could not parse date: {$dateValue}");
        return null;
        
    } catch (\Exception $e) {
        Log::warning("Date parsing error for '{$dateValue}': " . $e->getMessage());
        return null;
    }
}

/**
 * Enhanced phone number cleaning
 */
private function cleanPhoneNumber($phone)
{
    if (empty($phone) || is_null($phone)) {
        return null;
    }
    
    $phone = trim((string)$phone);
    
    // Handle null representations
    if (in_array(strtolower($phone), ['null', 'n/a', 'na', '', '-'])) {
        return null;
    }
    
    // Remove non-digit characters except +
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);
    
    // Basic validation - must be at least 6 digits
    if (strlen(str_replace('+', '', $cleaned)) < 6) {
        return null;
    }
    
    return $cleaned;
}

/**
 * Enhanced status mapping
 */
private function mapStatus($status)
{
    if (empty($status) || is_null($status)) {
        return 'active';
    }

    $status = strtolower(trim((string)$status));
    
    // Handle null representations
    if (in_array($status, ['null', 'n/a', 'na', '', '-'])) {
        return 'active';
    }
    
    $statusMappings = [
        // Active variations
        'active' => 'active',
        'working' => 'active',
        'online' => 'active',
        'ok' => 'active',
        'good' => 'active',
        'operational' => 'active',
        'up' => 'active',
        'running' => 'active',
        
        // Offline variations
        'offline' => 'offline',
        'down' => 'offline',
        'not working' => 'offline',
        'not seen' => 'offline',
        'inactive' => 'offline',
        'disconnected' => 'offline',
        
        // Faulty variations
        'faulty' => 'faulty',
        'broken' => 'faulty',
        'defective' => 'faulty',
        'error' => 'faulty',
        'damaged' => 'faulty',
        'failed' => 'faulty',
        
        // Maintenance variations
        'maintenance' => 'maintenance',
        'repair' => 'maintenance',
        'service' => 'maintenance',
        'servicing' => 'maintenance',
        'under repair' => 'maintenance',
        'in service' => 'maintenance',
    ];

    return $statusMappings[$status] ?? 'active';
}

/**
 * Enhanced preview with better error handling
 */
public function previewImport(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt|max:10240',
        'mapping_id' => 'nullable|exists:import_mappings,id',
        'preview_rows' => 'nullable|integer|min:1|max:10'
    ]);

    try {
        $file = $request->file('file');
        $mappingId = $request->mapping_id;
        $previewRows = $request->get('preview_rows', 5);

        // Get column mapping
        $columnMapping = null;
        if ($mappingId && class_exists('App\Models\ImportMapping')) {
            $columnMapping = ImportMapping::find($mappingId);
        }

        // Parse CSV with enhanced method
        $csvData = $this->parseCSVContent($file->getPathname());
        
        if (empty($csvData)) {
            return response()->json([
                'success' => false,
                'message' => 'CSV file is empty or could not be parsed'
            ], 400);
        }

        $headers = array_shift($csvData);
        $previewData = [];
        $rowCount = 0;

        foreach ($csvData as $rowIndex => $row) {
            if ($rowCount >= $previewRows) break;
            
            $rowNumber = $rowIndex + 2;
            
            // Skip empty rows
            if (empty(array_filter($row, function($val) { return $val !== null && $val !== ''; }))) {
                continue;
            }

            try {
                // Map data with error handling
                $mappedData = $columnMapping 
                    ? $this->mapRowDataDynamic($row, 1, $columnMapping, $rowNumber)
                    : $this->mapRowDataFixed($row, 1, $rowNumber);

                // Validate the mapped data
                $validation = $this->validateTerminalData($mappedData, $rowNumber);
                
                $previewData[] = [
                    'row_number' => $rowNumber,
                    'raw_data' => array_slice($row, 0, 8), // Show first 8 columns
                    'mapped_data' => $mappedData,
                    'validation_status' => $validation['valid'] ? 'valid' : 'error',
                    'validation_message' => $validation['valid'] ? 'OK' : $validation['error']
                ];

                $rowCount++;
                
            } catch (\Exception $e) {
                $previewData[] = [
                    'row_number' => $rowNumber,
                    'raw_data' => array_slice($row, 0, 8),
                    'mapped_data' => [],
                    'validation_status' => 'error',
                    'validation_message' => 'Mapping error: ' . $e->getMessage()
                ];
                $rowCount++;
            }
        }

        return response()->json([
            'success' => true,
            'headers' => $headers,
            'preview_data' => $previewData,
            'mapping_name' => $columnMapping ? $columnMapping->mapping_name : 'Default Mapping',
            'total_rows_in_file' => count($csvData)
        ]);

    } catch (\Exception $e) {
        Log::error('Preview import error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error previewing import: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Enhanced template download with proper headers
 */
public function downloadTemplate()
{
    $headers = [
        'Merchant ID',
        'Terminal ID', 
        'Type (from bank)',
        'Legal Name',
        'Client Full Name',
        'Address',
        'City',
        'Province',
        'Phone Number (from Bank)',
        'REGION',
        'Date',
        'Teams',
        'Device Type',
        'Serial Number',
        'Status',
        'Condition',
        'Issue Raised',
        'Comments',
        'Corrective Action',
        'Contact Person',
        'Contact Number'
    ];

    $filename = 'pos_terminals_import_template_' . date('Y-m-d') . '.csv';
    
    $response = response()->streamDownload(function() use ($headers) {
        $handle = fopen('php://output', 'w');
        
        // Write UTF-8 BOM for Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write sample data rows
        $sampleRows = [
            [
                '40103242444343',           // Merchant ID
                '77202134',                 // Terminal ID
                'Verifone',                 // Type(from bank)
                'SAMPLE BUSINESS LEGAL',    // Legal name
                'SAMPLE BUSINESS',          // Client Full Name
                '123 SAMPLE STREET',        // Address
                'HARARE',                   // City
                'Harare',                   // Province
                '263774033970',             // Phone Number(from Bank)
                'MT PLEASANT',              // REGION
                '19-Apr-2024',              // Date
                'Sample Team',              // Teams
                'VX-520',                   // Device Type
                'SN34323433',               // Serial Number
                'active',                   // Status
                'Good',                     // Condition
                '',                         // Issue Raised (empty)
                'Sample comments',          // Comments
                '',                         // Corrective Action (empty)
                'John Doe',                 // Contact Person
                '263778654664'              // Contact Number
            ],
            [
                '40103242444344',           
                '77202135',                 
                'Ingenico',                 
                'ANOTHER BUSINESS LEGAL',   
                'ANOTHER BUSINESS',         
                '456 ANOTHER STREET',       
                'BULAWAYO',                 
                'Bulawayo',                 
                '263712345678',             
                'HILLSIDE',                 
                '15-Mar-2024',              
                'Tech Team B',              
                'iWL220',                   
                'SN98765432',               
                'offline',                  
                'Needs attention',          
                'Card reader not working',  
                'Requires technician visit',
                'Replace card reader',      
                'Jane Smith',               
                '263777123456'              
            ]
        ];
        
        foreach ($sampleRows as $sampleRow) {
            fputcsv($handle, $sampleRow);
        }
        
        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);

    return $response;
}
}