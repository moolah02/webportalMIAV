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
    public function index(Request $request)
    {
        $query = PosTerminal::with(['client', 'regionModel']);

        // Apply filters
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

        $terminals = $query->paginate(20);

        // Get filter options
        $clients = Client::orderBy('company_name')->get();
        $regions = PosTerminal::distinct()->pluck('region')->filter()->sort();
        $cities = PosTerminal::distinct()->pluck('city')->filter()->sort();
        $provinces = PosTerminal::distinct()->pluck('province')->filter()->sort();

        // Get status options - handle if Category model doesn't exist
        $statusOptions = collect(['active', 'offline', 'faulty', 'maintenance']);
        try {
            if (class_exists('App\Models\Category')) {
                $statusOptions = Category::getSelectOptions(Category::TYPE_TERMINAL_STATUS);
            }
        } catch (\Exception $e) {
            Log::warning('Category model not available: ' . $e->getMessage());
        }

        // Get mappings for import tab
        $mappings = collect(); // Empty collection for now
        try {
            if (class_exists('App\Models\ImportMapping')) {
                $mappings = ImportMapping::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            Log::warning('ImportMapping model not available: ' . $e->getMessage());
        }

        // Statistics
        $stats = [
            'total_terminals' => PosTerminal::count(),
            'active_terminals' => PosTerminal::where('status', 'active')->count(),
            'faulty_terminals' => PosTerminal::whereIn('status', ['faulty', 'maintenance'])->count(),
            'offline_terminals' => PosTerminal::where('status', 'offline')->count(),
        ];

        return view('pos-terminals.index', compact(
            'terminals', 
            'clients', 
            'regions', 
            'cities', 
            'provinces', 
            'statusOptions',
            'mappings', // Add this
            'stats'
        ));
    }

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
     * Parse date value with multiple format support
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            // Handle various date formats
            $formats = [
                'Y-m-d',
                'd/m/Y', 
                'm/d/Y', 
                'd-m-Y', 
                'm-d-Y', 
                'd-M-y',
                'M-d',
                'd-M'
            ];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateValue);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try strtotime as last resort
            $timestamp = strtotime($dateValue);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$dateValue}");
            return null;
        }
    }

    /**
     * Map status values to valid statuses
     */
    private function mapStatus($status)
    {
        if (empty($status)) {
            return 'active';
        }

        $status = strtolower(trim((string)$status));
        
        $statusMappings = [
            'active' => 'active',
            'working' => 'active',
            'online' => 'active',
            'ok' => 'active',
            'good' => 'active',
            'operational' => 'active',
            
            'offline' => 'offline',
            'down' => 'offline',
            'not working' => 'offline',
            'not seen' => 'offline',
            
            'faulty' => 'faulty',
            'broken' => 'faulty',
            'defective' => 'faulty',
            'error' => 'faulty',
            
            'maintenance' => 'maintenance',
            'repair' => 'maintenance',
            'service' => 'maintenance',
            'servicing' => 'maintenance',
        ];

        return $statusMappings[$status] ?? 'active';
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'merchant ID',
            'Terminal ID', 
            'Type(from bank)',
            'Legal name',
            'Client Full Name',
            'Address',
            'City',
            'Province',
            'Phone Number(from Bank)',
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

        $filename = 'pos_terminals_import_template.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, $headers);
        
        // Add sample row matching your actual data structure
        fputcsv($handle, [
            '40103242444343',           // merchant ID
            '77202134',                 // Terminal ID
            'Verifone',                 // Type(from bank)
            'SAMPLE BUSINESS LEGAL',    // Legal name
            'SAMPLE BUSINESS',          // Client Full Name
            '123 SAMPLE STREET',        // Address
            'HARARE',                   // City
            'Harare',                   // Province
            '26377403397',              // Phone Number(from Bank)
            'MT PLEASANT',              // REGION
            '19-Apr',                   // Date
            'Sample Team',              // Teams
            'VX-520',                   // Device Type
            '34323433',                 // Serial Number
            'active',                   // Status
            'Good',                     // Condition
            '',                         // Issue Raised
            'Sample comments',          // Comments
            '',                         // Corrective Action
            'John Doe',                 // Contact Person
            '263778654664'              // Contact Number
        ]);
        
        fclose($handle);
        exit;
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
     * Preview import data using selected mapping
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

            // Get column mapping if specified
            $columnMapping = null;
            if ($mappingId && class_exists('App\Models\ImportMapping')) {
                $columnMapping = ImportMapping::find($mappingId);
            }

            // Read and parse first few rows
            $fileContent = file_get_contents($file->getPathname());
            
            $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
            }

            $lines = str_getcsv($fileContent, "\n");
            $headers = null;
            $previewData = [];
            $rowCount = 0;

            foreach ($lines as $lineIndex => $line) {
                if ($lineIndex === 0) {
                    $headers = str_getcsv($line, ",");
                    continue;
                }
                
                if ($rowCount >= $previewRows) {
                    break;
                }

                $row = str_getcsv($line, ",");
                
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map the data using the selected mapping or default
                if ($columnMapping) {
                    $mappedData = $this->mapRowDataDynamic($row, 1, $columnMapping, $lineIndex + 1);
                } else {
                    $mappedData = $this->mapRowData($row, 1, $lineIndex + 1);
                }

                $previewData[] = [
                    'row_number' => $lineIndex + 1,
                    'raw_data' => array_slice($row, 0, 10), // First 10 columns for display
                    'mapped_data' => $mappedData
                ];

                $rowCount++;
            }

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'preview_data' => $previewData,
                'mapping_name' => $columnMapping ? $columnMapping->mapping_name : 'Default Mapping',
                'total_rows_in_file' => count($lines) - 1 // Exclude header
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
     * Enhanced import method with dynamic column mapping
     */
    public function import(Request $request)
    {
        Log::info('Import method called with dynamic mapping');
        
        try {
            // Validate the request
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:10240',
                'client_id' => 'required|exists:clients,id',
                'mapping_id' => 'nullable|exists:import_mappings,id', // Optional mapping
                'options' => 'nullable|array'
            ]);

            if (!$request->hasFile('file')) {
                return back()->with('error', 'No file was uploaded.');
            }

            $file = $request->file('file');
            $clientId = $request->client_id;
            $mappingId = $request->mapping_id;
            $options = $request->options ?? [];

            // Get column mapping if specified
            $columnMapping = null;
            if ($mappingId && class_exists('App\Models\ImportMapping')) {
                $columnMapping = ImportMapping::find($mappingId);
            }

            Log::info('Processing file with mapping: ' . ($columnMapping ? $columnMapping->mapping_name : 'Default'));

            // Process the import with dynamic mapping
            $results = $this->processCSVImportWithMapping($file->getPathname(), $clientId, $options, $columnMapping);

            $message = "Import completed! Created: {$results['created']}, Updated: {$results['updated']}, Skipped: {$results['skipped']}, Errors: " . count($results['errors']);

            if (!empty($results['errors'])) {
                return back()
                    ->with('success', $message)
                    ->with('import_errors', array_slice($results['errors'], 0, 10));
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Process CSV import with proper encoding and validation - FIXED
     */
    private function processCSVImport($filePath, int $clientId, array $options)
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
        
        // Detect and convert encoding if needed
        $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
            Log::info("Converted file from {$encoding} to UTF-8");
        }

        // Parse CSV from content
        $lines = str_getcsv($fileContent, "\n");
        $rowNumber = 0;
        $headers = null;

        foreach ($lines as $line) {
            $rowNumber++;
            
            // Parse each line
            $row = str_getcsv($line, ",");
            
            // Skip header row
            if ($rowNumber === 1) {
                $headers = $row;
                Log::info('Headers: ' . json_encode($headers));
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($row)) || count($row) < 2) {
                Log::info("Skipping empty row {$rowNumber}");
                continue;
            }

            try {
                // Map the row data with debugging
                $terminalData = $this->mapRowData($row, $clientId, $rowNumber);
                
                Log::info("Row {$rowNumber} mapped data: " . json_encode($terminalData));

                // Validate required fields
                if (empty($terminalData['terminal_id'])) {
                    $columnB = isset($row[1]) ? $row[1] : 'N/A';
                    $results['errors'][] = "Row {$rowNumber}: Terminal ID is empty (Column B: '{$columnB}')";
                    continue;
                }

                if (empty($terminalData['merchant_name'])) {
                    $columnE = isset($row[4]) ? $row[4] : 'N/A';
                    $results['errors'][] = "Row {$rowNumber}: Merchant name is empty (Column E: '{$columnE}')";
                    continue;
                }

                // Check if terminal exists
                $existingTerminal = PosTerminal::where('terminal_id', $terminalData['terminal_id'])->first();

                if ($existingTerminal) {
                    if ($skipDuplicates && !$updateExisting) {
                        $results['skipped']++;
                        Log::info("Skipped duplicate: {$terminalData['terminal_id']}");
                        continue;
                    }

                    if ($updateExisting) {
                        $existingTerminal->update($terminalData);
                        $results['updated']++;
                        Log::info("Updated terminal: {$terminalData['terminal_id']}");
                    } else {
                        $results['errors'][] = "Row {$rowNumber}: Terminal ID '{$terminalData['terminal_id']}' already exists";
                    }
                } else {
                    // Create new terminal
                    $terminal = PosTerminal::create($terminalData);
                    $results['created']++;
                    Log::info("Created terminal: {$terminalData['terminal_id']} (ID: {$terminal->id})");
                }

            } catch (\Exception $e) {
                $error = "Row {$rowNumber}: " . $e->getMessage();
                $results['errors'][] = $error;
                Log::error($error);
            }
        }

        return $results;
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

    private function cleanValue($value)
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        
        $cleaned = trim((string)$value);
        return $cleaned === '' ? null : $cleaned;
    }

    /**
     * Clean phone number
     */
    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Convert to string and remove any non-digit characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', (string)$phone);
        
        return strlen($cleaned) > 5 ? $cleaned : null;
    }
}