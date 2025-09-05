<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\ImportMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use Illuminate\Support\Arr;


class PosTerminalImportController extends Controller
{
    // Define your template - these are the fields your system recognizes
    private const FIELD_TEMPLATE = [
        // Required fields
        'terminal_id' => ['required' => true, 'database_field' => 'terminal_id'],
        'merchant_name' => ['required' => true, 'database_field' => 'merchant_name'],

        // Standard optional fields
        'merchant_id' => ['required' => false, 'database_field' => 'merchant_id'],
        'client_id' => ['required' => false, 'database_field' => 'client_id'],
        'legal_name' => ['required' => false, 'database_field' => 'legal_name'],
        'merchant_contact_person' => ['required' => false, 'database_field' => 'merchant_contact_person'],
        'merchant_phone' => ['required' => false, 'database_field' => 'merchant_phone'],
        'merchant_email' => ['required' => false, 'database_field' => 'merchant_email'],
        'physical_address' => ['required' => false, 'database_field' => 'physical_address'],
        'city' => ['required' => false, 'database_field' => 'city'],
        'province' => ['required' => false, 'database_field' => 'province'],
        'area' => ['required' => false, 'database_field' => 'area'],
        'region' => ['required' => false, 'database_field' => 'region'],
        'business_type' => ['required' => false, 'database_field' => 'business_type'],
        'installation_date' => ['required' => false, 'database_field' => 'installation_date'],
        'terminal_model' => ['required' => false, 'database_field' => 'terminal_model'],
        'serial_number' => ['required' => false, 'database_field' => 'serial_number'],
        'contract_details' => ['required' => false, 'database_field' => 'contract_details'],
        'status' => ['required' => false, 'database_field' => 'status'],
        'current_status' => ['required' => false, 'database_field' => 'current_status'],
        'deployment_status' => ['required' => false, 'database_field' => 'deployment_status'],
        'condition_status' => ['required' => false, 'database_field' => 'condition_status'],
        'issues_raised' => ['required' => false, 'database_field' => 'issues_raised'],
        'corrective_action' => ['required' => false, 'database_field' => 'corrective_action'],
        'site_contact_person' => ['required' => false, 'database_field' => 'site_contact_person'],
        'site_contact_number' => ['required' => false, 'database_field' => 'site_contact_number'],
        'last_service_date' => ['required' => false, 'database_field' => 'last_service_date'],
        'next_service_due' => ['required' => false, 'database_field' => 'next_service_due'],
        'last_visit_date' => ['required' => false, 'database_field' => 'last_visit_date'],
    ];

    // Header patterns for smart detection
    private const HEADER_PATTERNS = [
        'terminal_id' => [
            'exact' => ['terminal_id', 'terminalid', 'terminal id', 'tid', 'terminal number', 'terminal no'],
            'patterns' => ['/terminal.*id/i', '/^tid$/i', '/terminal.*number/i', '/terminal.*no/i'],
            'validation' => 'terminal_id'
        ],
        'merchant_name' => [
            'exact' => ['merchant_name', 'merchant name', 'business_name', 'business name', 'name', 'merchantname', 'company name', 'client full name'],
            'patterns' => ['/merchant.*name/i', '/business.*name/i', '/company.*name/i', '/^name$/i', '/client.*name/i'],
            'validation' => 'text'
        ],
        'merchant_id' => [
            'exact' => ['merchant_id', 'merchant id', 'merchantid', 'merchant number'],
            'patterns' => ['/merchant.*id/i', '/merchant.*number/i'],
            'validation' => 'text'
        ],
        'legal_name' => [
            'exact' => ['legal_name', 'legal name', 'legal business name', 'registered name'],
            'patterns' => ['/legal.*name/i', '/registered.*name/i'],
            'validation' => 'text'
        ],
        'merchant_contact_person' => [
            'exact' => ['contact_person', 'contact person', 'merchant_contact_person', 'merchant contact person', 'contact name'],
            'patterns' => ['/contact.*person/i', '/merchant.*contact/i', '/contact.*name/i'],
            'validation' => 'text'
        ],
        'merchant_phone' => [
            'exact' => ['phone', 'telephone', 'mobile', 'merchant_phone', 'contact_number', 'phone number from bank'],
            'patterns' => ['/phone/i', '/telephone/i', '/mobile/i', '/contact.*number/i'],
            'validation' => 'phone'
        ],
        'merchant_email' => [
            'exact' => ['email', 'email_address', 'merchant_email', 'e-mail'],
            'patterns' => ['/email/i', '/e-mail/i'],
            'validation' => 'email'
        ],
        'physical_address' => [
            'exact' => ['address', 'physical_address', 'location', 'street', 'street address'],
            'patterns' => ['/address/i', '/location/i', '/street/i'],
            'validation' => 'text'
        ],
        'city' => [
            'exact' => ['city', 'town'],
            'patterns' => ['/^city$/i', '/^town$/i'],
            'validation' => 'text'
        ],
        'province' => [
            'exact' => ['province', 'state'],
            'patterns' => ['/province/i', '/state/i'],
            'validation' => 'text'
        ],
        'region' => [
            'exact' => ['region', 'area', 'zone'],
            'patterns' => ['/region/i', '/area/i', '/zone/i'],
            'validation' => 'text'
        ],
        'business_type' => [
            'exact' => ['business_type', 'type', 'category', 'type from bank'],
            'patterns' => ['/business.*type/i', '/^type$/i', '/category/i'],
            'validation' => 'text'
        ],
        'installation_date' => [
            'exact' => ['installation_date', 'install_date', 'date_installed', 'date', 'installation date'],
            'patterns' => ['/installation.*date/i', '/install.*date/i', '/date.*install/i', '/^date$/i'],
            'validation' => 'date'
        ],
        'terminal_model' => [
            'exact' => ['terminal_model', 'model', 'device_model', 'device type'],
            'patterns' => ['/terminal.*model/i', '/device.*model/i', '/^model$/i', '/device.*type/i'],
            'validation' => 'text'
        ],
        'serial_number' => [
            'exact' => ['serial_number', 'serial', 'sn', 'serial no'],
            'patterns' => ['/serial.*number/i', '/^serial$/i', '/^sn$/i', '/serial.*no/i'],
            'validation' => 'text'
        ],
        'status' => [
            'exact' => ['status', 'state', 'condition', 'terminal status'],
            'patterns' => ['/^status$/i', '/^state$/i', '/terminal.*status/i'],
            'validation' => 'status'
        ],
        'current_status' => [
            'exact' => ['current_status', 'current status'],
            'patterns' => ['/current.*status/i'],
            'validation' => 'status'
        ],
        'deployment_status' => [
            'exact' => ['deployment_status', 'deployment status'],
            'patterns' => ['/deployment.*status/i'],
            'validation' => 'text'
        ],
        'condition_status' => [
            'exact' => ['condition_status', 'condition status', 'condition'],
            'patterns' => ['/condition.*status/i', '/^condition$/i'],
            'validation' => 'text'
        ],
        'issues_raised' => [
            'exact' => ['issues_raised', 'issues raised', 'issues', 'problems', 'issue raised'],
            'patterns' => ['/issues.*raised/i', '/^issues$/i', '/problems/i', '/issue.*raised/i'],
            'validation' => 'text'
        ],
        'corrective_action' => [
            'exact' => ['corrective_action', 'corrective action', 'action', 'actions'],
            'patterns' => ['/corrective.*action/i', '/^action$/i', '/^actions$/i'],
            'validation' => 'text'
        ],
        'site_contact_person' => [
            'exact' => ['site_contact_person', 'site contact person', 'site contact', 'contact person'],
            'patterns' => ['/site.*contact.*person/i', '/site.*contact/i'],
            'validation' => 'text'
        ],
        'site_contact_number' => [
            'exact' => ['site_contact_number', 'site contact number', 'site phone', 'contact number'],
            'patterns' => ['/site.*contact.*number/i', '/site.*phone/i'],
            'validation' => 'phone'
        ]
    ];



public function import(Request $request)
{
    // Optimize for large files
    set_time_limit(600); // 10 minutes
    ini_set('memory_limit', '2048M');
    ini_set('max_input_time', 600);

    Log::info('=== SMART IMPORT STARTED ===', [
        'timestamp' => now(),
        'has_file' => $request->hasFile('file'),
        'client_id' => $request->input('client_id'),
        'mapping_id' => $request->input('mapping_id'),
        'request_size' => strlen(serialize($_REQUEST)),
        'options' => $request->input('options', [])
    ]);

    try {
        // Validation
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:102400', // 100MB max
            'client_id' => 'required|exists:clients,id',
            'mapping_id' => 'nullable|exists:import_mappings,id',
            'options' => 'array',
            'options.*' => 'in:skip_duplicates,update_existing',
        ]);

        Log::info('Validation passed');

        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            Log::error('File validation failed');
            return back()->with('error', 'Invalid file upload');
        }

        $file = $request->file('file');
        $client = Client::findOrFail($request->client_id);
        $mapping = $request->filled('mapping_id') ? ImportMapping::find($request->mapping_id) : null;
        $skipDuplicates = in_array('skip_duplicates', $request->options ?? [], true);
        $updateExisting = in_array('update_existing', $request->options ?? [], true);

        Log::info('Import parameters', [
            'client' => $client->company_name,
            'mapping' => $mapping?->mapping_name ?? 'Smart Header Detection',
            'file_size' => $file->getSize(),
            'file_name' => $file->getClientOriginalName(),
            'skip_duplicates' => $skipDuplicates,
            'update_existing' => $updateExisting
        ]);

        // Read file with streaming for large files
        try {
            [$headers, $totalRows] = $this->readFileStream($file->getRealPath());

            Log::info('File structure detected', [
                'headers' => $headers,
                'total_rows' => $totalRows,
                'columns' => count($headers)
            ]);

            if (empty($headers)) {
                throw new \Exception('No headers found in file');
            }

        } catch (\Throwable $e) {
            Log::error('File reading failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Could not read file: ' . $e->getMessage());
        }

        // Detect column mapping
        $columnMapping = $mapping ?
            $this->useCustomMapping($mapping) :
            $this->detectHeaderMapping($headers);

        Log::info('Column mapping result', [
            'mapped_fields' => array_keys($columnMapping['mapped']),
            'extra_fields' => array_keys($columnMapping['extra']),
            'missing_required' => $columnMapping['missing_required']
        ]);

        // Check for required fields
        if (!empty($columnMapping['missing_required'])) {
            $errorMessage = 'Missing required fields: ' . implode(', ', $columnMapping['missing_required']) .
                '. Available columns: ' . implode(', ', $headers);
            Log::error($errorMessage);
            return back()->with('error', $errorMessage);
        }

        // Process with chunked reading for large files
        $result = $this->processFileInChunks($file->getRealPath(), $columnMapping, $client->id, [
            'skip_duplicates' => $skipDuplicates,
            'update_existing' => $updateExisting
        ]);

        Log::info('Import completed successfully');
        return $result;

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return back()->withErrors($e->errors())->with('error', 'Validation failed');

    } catch (\Exception $e) {
        Log::error('Import failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}



public function preview(Request $request)
{
    // Increase limits
    set_time_limit(300);
    ini_set('memory_limit', '1024M');

    // Log the start
    Log::info('=== PREVIEW STARTED ===', [
        'timestamp' => now(),
        'has_file' => $request->hasFile('file'),
        'client_id' => $request->input('client_id'),
        'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : 'no file',
    ]);

    try {
        // Basic validation first
        if (!$request->hasFile('file')) {
            Log::error('No file in request');
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 422);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            Log::error('Invalid file upload', ['error' => $file->getError()]);
            return response()->json([
                'success' => false,
                'message' => 'File upload error: ' . $file->getErrorMessage()
            ], 422);
        }

        Log::info('File validation passed', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);

        // Validate request data
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200', // 50MB
            'client_id' => 'required|exists:clients,id',
            'mapping_id' => 'nullable|exists:import_mappings,id',
            'preview_rows' => 'nullable|integer|min:1|max:10',
        ]);

        Log::info('Request validation passed');

        // Get the client
        $client = Client::findOrFail($validated['client_id']);
        $mapping = $request->filled('mapping_id') ? ImportMapping::find($validated['mapping_id']) : null;
        $previewRows = (int)($validated['preview_rows'] ?? 5);

        Log::info('Parameters loaded', [
            'client' => $client->company_name,
            'mapping' => $mapping?->mapping_name ?? 'Smart Detection',
            'preview_rows' => $previewRows
        ]);

        // Try to read the file
        try {
            $filePath = $file->getRealPath();

            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('File not accessible at: ' . $filePath);
            }

            Log::info('Attempting to read file', ['path' => basename($filePath)]);

            // Check if PhpSpreadsheet classes are available
            if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                throw new \Exception('PhpSpreadsheet library not found. Please install it with: composer require phpoffice/phpspreadsheet');
            }

            [$headers, $sampleRows] = $this->readSampleRows($filePath, $previewRows);

            Log::info('File read successfully', [
                'headers_count' => count($headers),
                'sample_rows_count' => count($sampleRows),
                'headers' => array_slice($headers, 0, 5) // Log first 5 headers
            ]);

        } catch (\Exception $e) {
            Log::error('File reading failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not read file: ' . $e->getMessage()
            ], 500);
        }

        if (empty($headers)) {
            return response()->json([
                'success' => false,
                'message' => 'No headers found in file. Please ensure the first row contains column headers.'
            ], 422);
        }

        // Detect column mapping
        try {
            $columnMapping = $mapping ?
                $this->useCustomMapping($mapping) :
                $this->detectHeaderMapping($headers);

            Log::info('Column mapping completed', [
                'mapped_fields' => array_keys($columnMapping['mapped']),
                'missing_required' => $columnMapping['missing_required']
            ]);
        } catch (\Exception $e) {
            Log::error('Column mapping failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Column mapping failed: ' . $e->getMessage()
            ], 500);
        }

        // Generate preview data
        $preview = [];
        foreach ($sampleRows as $i => $row) {
            try {
                $mapped = $this->mapSingleRow($row, $client->id, $columnMapping);
                $validation = $this->validateRowData($mapped, $i + 2);

                $preview[] = [
                    'row_number' => $i + 2,
                    'raw_data' => array_slice($row, 0, 8),
                    'mapped_data' => $mapped,
                    'validation_status' => $validation['valid'] ? 'valid' : 'error',
                    'validation_message' => $validation['valid'] ? 'OK' : $validation['error'],
                ];
            } catch (\Exception $e) {
                Log::warning('Row mapping error', [
                    'row' => $i + 2,
                    'error' => $e->getMessage()
                ]);

                $preview[] = [
                    'row_number' => $i + 2,
                    'raw_data' => array_slice($row, 0, 8),
                    'mapped_data' => [],
                    'validation_status' => 'error',
                    'validation_message' => 'Mapping error: ' . $e->getMessage(),
                ];
            }
        }

        Log::info('Preview generated successfully', ['preview_count' => count($preview)]);

        return response()->json([
            'success' => true,
            'mapping_name' => $mapping?->mapping_name ?? 'Smart Header Detection',
            'headers' => $headers,
            'preview_data' => $preview,
            'column_mapping_info' => [
                'mapped_fields' => array_keys($columnMapping['mapped']),
                'extra_fields' => array_keys($columnMapping['extra']),
                'missing_required' => $columnMapping['missing_required'] ?? []
            ]
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Preview validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
          'message' => 'Validation failed: ' . implode(', ', Arr::flatten($e->errors()))

        ], 422);

    } catch (\Exception $e) {
        Log::error('Preview failed with exception', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Preview failed: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Smart header detection - the core of the system
     */
    private function detectHeaderMapping(array $headers): array
    {
        $mapped = [];
        $extra = [];
        $usedIndices = [];

        Log::info('Starting header detection', ['headers' => $headers]);

        // First pass: exact matches
        foreach ($headers as $index => $header) {
            $normalizedHeader = $this->normalizeHeader($header);

            foreach (self::HEADER_PATTERNS as $field => $patterns) {
                if (in_array($normalizedHeader, $patterns['exact'])) {
                    $mapped[$field] = $index;
                    $usedIndices[] = $index;
                    Log::debug("Exact match: '{$header}' -> {$field}");
                    break;
                }
            }
        }

        // Second pass: pattern matches for unmapped headers
        foreach ($headers as $index => $header) {
            if (in_array($index, $usedIndices)) continue;

            $normalizedHeader = $this->normalizeHeader($header);

            foreach (self::HEADER_PATTERNS as $field => $patterns) {
                if (isset($mapped[$field])) continue; // Already mapped

                foreach ($patterns['patterns'] as $pattern) {
                    if (preg_match($pattern, $normalizedHeader)) {
                        $mapped[$field] = $index;
                        $usedIndices[] = $index;
                        Log::debug("Pattern match: '{$header}' -> {$field}");
                        break 2;
                    }
                }
            }
        }

        // Third pass: Store unmapped columns as extra fields
        foreach ($headers as $index => $header) {
            if (!in_array($index, $usedIndices)) {
                $fieldName = $this->sanitizeFieldName($header);
                $extra[$fieldName] = $index;
                Log::debug("Extra field: '{$header}' -> {$fieldName}");
            }
        }

        // Check for missing required fields
        $missingRequired = [];
        foreach (self::FIELD_TEMPLATE as $field => $config) {
            if ($config['required'] && !isset($mapped[$field])) {
                $missingRequired[] = $field;
            }
        }

        return [
            'mapped' => $mapped,
            'extra' => $extra,
            'missing_required' => $missingRequired
        ];
    }

    /**
     * Process file in chunks for large files
     */
    private function processFileInChunks(string $filePath, array $columnMapping, int $clientId, array $options): \Illuminate\Http\RedirectResponse
{
    $chunkSize = 500;
    $created = 0; $updated = 0; $skipped = 0; $errors = 0;
    $errorMsgs = [];

    try {
        $reader = $this->createStreamReader($filePath);

        // consume header row exactly once
        $headerRow = $reader->getNextRow();

        $rowCount = 0;
        $chunk = [];

        while (($row = $reader->getNextRow()) !== null) {
            $rowCount++;

            // REMOVE this (it was dropping your first data row):
            // if ($rowCount === 1) continue;

            $chunk[] = ['row' => $row, 'number' => $rowCount + 1]; // +1 to reflect actual CSV row number

            if (count($chunk) >= $chunkSize || $reader->isEOF()) {
                $chunkResult = $this->processChunk($chunk, $columnMapping, $clientId, $options);
                $created += $chunkResult['created'];
                $updated += $chunkResult['updated'];
                $skipped += $chunkResult['skipped'];
                $errors  += $chunkResult['errors'];
                $errorMsgs = array_merge($errorMsgs, $chunkResult['errorMsgs']);
                $chunk = [];
                if ($rowCount % 2000 === 0) gc_collect_cycles();
            }
        }

    } catch (\Throwable $e) {
        Log::error('Chunked processing failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Processing failed: ' . $e->getMessage());
    }

    $summary = "Import completed — Created: {$created}, Updated: {$updated}, Skipped: {$skipped}, Errors: {$errors}";
    if ($errors > 0) {
        $errorSample = implode(' | ', array_slice($errorMsgs, 0, 3));
        $summary .= "\n\nError sample: " . $errorSample;
    }
    $messageType = ($errors > $created) ? 'error' : 'success';

    return redirect()->route('pos-terminals.index')->with($messageType, $summary);
}


    /**
     * Process a chunk of rows
     */
    private function processChunk(array $chunk, array $columnMapping, int $clientId, array $options): array
    {
        $created = 0; $updated = 0; $skipped = 0; $errors = 0; $errorMsgs = [];

        DB::beginTransaction();
        try {
            foreach ($chunk as $item) {
                $row = $item['row'];
                $rowNumber = $item['number'];

                try {
                    $data = $this->mapSingleRow($row, $clientId, $columnMapping);
                    $validation = $this->validateRowData($data, $rowNumber);

                    if (!$validation['valid']) {
                        $errors++;
                        $errorMsgs[] = $validation['error'];
                        continue;
                    }

                    $existing = PosTerminal::where('terminal_id', $data['terminal_id'])->first();

                    if ($existing) {
                        if ($options['skip_duplicates'] && !$options['update_existing']) {
                            $skipped++;
                        } elseif ($options['update_existing']) {
                            $existing->fill($data)->save();
                            $updated++;
                        } else {
                            $errors++;
                            $errorMsgs[] = "Row {$rowNumber}: Terminal {$data['terminal_id']} already exists";
                        }
                        continue;
                    }

                    PosTerminal::create($data);
                    $created++;

                } catch (\Throwable $e) {
                    $errors++;
                    $errorMsgs[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return compact('created', 'updated', 'skipped', 'errors', 'errorMsgs');
    }

    /**
     * Map a single row using the column mapping
     */
    private function mapSingleRow(array $row, int $clientId, array $columnMapping): array
    {
        $data = ['client_id' => $clientId];
        $extraFields = [];

        // Map standard fields
        foreach ($columnMapping['mapped'] as $field => $columnIndex) {
            if (isset($row[$columnIndex])) {
                $value = $this->processFieldValue($row[$columnIndex], $field);
                if ($value !== null) {
                    $dbField = self::FIELD_TEMPLATE[$field]['database_field'];
                    $data[$dbField] = $value;
                }
            }
        }

        // Map extra fields
        foreach ($columnMapping['extra'] as $fieldName => $columnIndex) {
            if (isset($row[$columnIndex])) {
                $value = $this->cleanValue($row[$columnIndex]);
                if ($value !== null) {
                    $extraFields[$fieldName] = $value;
                }
            }
        }

        // Store extra fields as JSON
        if (!empty($extraFields)) {
            $data['extra_fields'] = json_encode($extraFields);
        }

        // Set defaults
        if (empty($data['status'])) $data['status'] = 'active';
        if (empty($data['current_status'])) $data['current_status'] = $data['status'];

        return $data;
    }

    /**
     * Process field values based on field type
     */
    private function processFieldValue($value, string $fieldType)
    {
        $value = $this->cleanValue($value);
        if ($value === null) return null;

        $patterns = self::HEADER_PATTERNS[$fieldType] ?? null;
        if (!$patterns) return $value;

        switch ($patterns['validation']) {
            case 'phone':
                return $this->cleanPhoneNumber($value);
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ?: null;
            case 'date':
                return $this->parseDate($value);
            case 'status':
                return $this->mapStatus($value);
            case 'terminal_id':
                return strlen($value) >= 3 ? $value : null;
            default:
                return $value;
        }
    }

    /**
     * Stream reader for large files
     */
/**
 * Stream reader for large files (CSV/XLSX/XLS)
 */
private function createStreamReader(string $filePath): object
{
    $type   = IOFactory::identify($filePath);
    $reader = IOFactory::createReader($type);

    if (method_exists($reader, 'setReadDataOnly')) {
        $reader->setReadDataOnly(true);
    }

    if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
        $reader->setDelimiter(',');
        $reader->setEnclosure('"');
        if (method_exists($reader, 'setSheetIndex')) {
            $reader->setSheetIndex(0);
        }
    }

    // IMPORTANT: don't skip the first row here. Let callers decide.
    return new class($reader, $filePath) {
        private $reader;
        private $spreadsheet;
        private $worksheet;
        private $rowIterator;

        public function __construct($reader, $filePath)
        {
            $this->reader       = $reader;
            $this->spreadsheet  = $reader->load($filePath);
            $this->worksheet    = $this->spreadsheet->getActiveSheet();
            $this->rowIterator  = $this->worksheet->getRowIterator();
            $this->rowIterator->rewind(); // now current row is header
        }

        public function getNextRow(): ?array
        {
            if (!$this->rowIterator->valid()) {
                return null;
            }
            $row   = $this->rowIterator->current();
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getCalculatedValue();
            }
            $this->rowIterator->next();
            return $cells;
        }

        public function isEOF(): bool
        {
            return !$this->rowIterator->valid();
        }
    };
}



private function readSampleRows(string $filePath, int $sampleSize = 5): array
{
    try {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception('File not found or not readable');
        }

        $type = IOFactory::identify($filePath);
        Log::info('File type identified', ['type' => $type, 'file' => basename($filePath)]);

        $reader = IOFactory::createReader($type);

        // Configure reader
        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(true);
        }

        // CSV-specific settings
        if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setEscapeCharacter('\\');

            // Try to detect encoding
            if (function_exists('mb_detect_encoding')) {
                $content = file_get_contents($filePath, false, null, 0, 1024);
                $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16', 'Windows-1252', 'ISO-8859-1'], true);
                if ($encoding && method_exists($reader, 'setInputEncoding')) {
                    $reader->setInputEncoding($encoding);
                }
            }
        }

        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Get all data as array
        $allData = $sheet->toArray(null, true, true, false);

        if (empty($allData)) {
            throw new \Exception('File appears to be empty or contains no readable data');
        }

        // Clean and normalize headers
        $headers = array_shift($allData);
        $headers = array_map(function($header) {
            return $this->normalizeHeader(trim((string)($header ?? '')));
        }, $headers);

        // Filter out completely empty headers
        $headers = array_filter($headers, function($header) {
            return !empty($header) && $header !== '';
        });

        if (empty($headers)) {
            throw new \Exception('No valid column headers found in the first row');
        }

        // Get sample rows and clean them
        $sampleRows = array_slice($allData, 0, $sampleSize);
        $sampleRows = array_map(function($row) {
            return array_map(function($cell) {
                return is_string($cell) ? trim($cell) : $cell;
            }, $row);
        }, $sampleRows);

        Log::info('Sample data read successfully', [
            'headers_count' => count($headers),
            'sample_rows' => count($sampleRows)
        ]);

        return [array_values($headers), $sampleRows];

    } catch (\Exception $e) {
        Log::error('Error reading sample rows', [
            'error' => $e->getMessage(),
            'file' => basename($filePath)
        ]);
        throw new \Exception('Could not read file: ' . $e->getMessage());
    }
}

    private function readFileStream(string $filePath): array
{
    $reader = $this->createStreamReader($filePath);

    // real header row
    $headers = $reader->getNextRow();

    $rowCount = 0;
    while ($reader->getNextRow() !== null) {
        $rowCount++;
    }

    $normalizedHeaders = array_map(fn($h) => $this->normalizeHeader((string)($h ?? '')), $headers ?? []);
    return [$normalizedHeaders, $rowCount];
}


    // Helper methods
    private function normalizeHeader(string $header): string
    {
        $header = mb_strtolower(trim($header));
        $header = str_replace(['  ', '-', '/', '\\', '(', ')'], ' ', $header);
        return preg_replace('/\s+/', ' ', trim($header));
    }

    private function sanitizeFieldName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9_]/', '_', $name);
        return preg_replace('/_{2,}/', '_', trim($name, '_'));
    }

    private function cleanValue($value)
    {
        if ($value === null) return null;
        $v = trim((string)$value);
        if ($v === '') return null;
        $nulls = ['null', 'n/a', 'na', '-', 'nil', 'none', 'empty'];
        return in_array(mb_strtolower($v), $nulls, true) ? null : $v;
    }

    private function validateRowData(array $data, int $rowNumber): array
    {
        if (empty($data['terminal_id'])) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Terminal ID is required"];
        }
        if (empty($data['merchant_name'])) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Merchant name is required"];
        }
        if (!empty($data['merchant_email']) && !filter_var($data['merchant_email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Invalid email format"];
        }
        return ['valid' => true];
    }

    private function useCustomMapping(ImportMapping $mapping): array
    {
        $mappings = (array)($mapping->column_mappings ?? []);
        return [
            'mapped' => $mappings,
            'extra' => [],
            'missing_required' => []
        ];
    }

    private function parseDate($value)
    {
        if ($value === null) return null;
        $v = trim((string)$value);
        if ($v === '' || in_array(mb_strtolower($v), ['null', 'n/a', 'na', '-'], true)) return null;

        // Try common formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'd-M-Y', 'd-M-y', 'd-M', 'j-M', 'M-d', 'Y/m/d', 'd.m.Y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $v);
            if ($date) {
                // For formats without year, assume current year
                if (!str_contains($format, 'Y') && !str_contains($format, 'y')) {
                    $date->setDate((int)date('Y'), (int)$date->format('n'), (int)$date->format('j'));
                }
                return $date->format('Y-m-d');
            }
        }

        $timestamp = strtotime($v);
        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function cleanPhoneNumber($phone)
    {
        if ($phone === null) return null;
        $clean = preg_replace('/[^0-9+]/', '', (string)$phone);
        return (strlen(str_replace('+', '', $clean)) >= 6) ? $clean : null;
    }

    private function mapStatus($status)
    {
        if ($status === null) return 'active';
        $s = mb_strtolower(trim((string)$status));
        if ($s === '' || in_array($s, ['null', 'n/a', 'na', '-'], true)) return 'active';

        $statusMap = [
            'active' => 'active', 'working' => 'active', 'online' => 'active', 'ok' => 'active',
            'good' => 'active', 'operational' => 'active', 'up' => 'active', 'running' => 'active',
            'offline' => 'offline', 'down' => 'offline', 'inactive' => 'offline', 'not working' => 'offline',
            'not seen' => 'offline', 'disconnected' => 'offline',
            'faulty' => 'faulty', 'broken' => 'faulty', 'error' => 'faulty', 'damaged' => 'faulty',
            'defective' => 'faulty', 'failed' => 'faulty',
            'maintenance' => 'maintenance', 'repair' => 'maintenance', 'service' => 'maintenance',
            'servicing' => 'maintenance', 'under repair' => 'maintenance', 'in service' => 'maintenance'
        ];

        return $statusMap[$s] ?? 'active';
    }

    /**
     * Download a CSV template
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
            'Contact Number',
        ];

        $filename = 'pos_terminals_import_template_'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($headers) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            // Sample rows
            fputcsv($out, [
                '40103242444343','77202134','Verifone','SAMPLE BUSINESS LEGAL','SAMPLE BUSINESS',
                '123 SAMPLE STREET','HARARE','Harare','263774033970','MT PLEASANT','19-Apr-2024','Team A',
                'VX-520','SN34323433','active','Good','','Sample comments','','John Doe','263778654664'
            ]);
            fputcsv($out, [
                '40103242444344','77202135','Ingenico','ANOTHER BUSINESS LEGAL','ANOTHER BUSINESS',
                '456 ANOTHER STREET','BULAWAYO','Bulawayo','263712345678','HILLSIDE','15-Mar-2024','Team B',
                'iWL220','SN98765432','offline','Needs attention','Card reader not working',
                'Requires technician visit','Replace card reader','Jane Smith','263777123456'
            ]);

            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }
}
