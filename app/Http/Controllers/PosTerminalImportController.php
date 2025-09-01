<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PosTerminal;
use App\Models\ImportMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PosTerminalImportController extends Controller
{


    /**
     * Process the full file and write to DB (CSV, XLSX, XLS)
     */
public function import(Request $request)
{
    // Handle large files
    set_time_limit(300);
    ini_set('memory_limit', '1024M');

    // STEP 1: Log everything that comes in
    Log::info('=== IMPORT REQUEST RECEIVED ===', [
        'timestamp' => now(),
        'has_file' => $request->hasFile('file'),
        'client_id' => $request->input('client_id'),
        'mapping_id' => $request->input('mapping_id'),
        'options' => $request->input('options'),
        'request_size' => $request->header('Content-Length'),
        'all_files' => $request->allFiles()
    ]);

    // STEP 2: Try validation and catch any errors
    try {
        $request->validate([
            'file'       => 'required|file|mimes:csv,txt,xlsx,xls',
            'client_id'  => 'required|exists:clients,id',
            'mapping_id' => 'nullable|exists:import_mappings,id',
            'options'    => 'array',
            'options.*'  => 'in:skip_duplicates,update_existing',
        ]);

        Log::info('Validation passed');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return back()->withErrors($e->errors())->with('error', 'Validation failed: ' . implode(', ', array_flatten($e->errors())));
    }

    // STEP 3: Check file upload
    if (!$request->hasFile('file')) {
        Log::error('No file in request');
        return back()->with('error', 'No file was uploaded. Check file size limits (current: ' . ini_get('upload_max_filesize') . ')');
    }

    $file = $request->file('file');

    Log::info('File details', [
        'name' => $file->getClientOriginalName(),
        'size' => $file->getSize(),
        'error' => $file->getError(),
        'is_valid' => $file->isValid(),
        'mime_type' => $file->getMimeType()
    ]);

    if (!$file->isValid()) {
        Log::error('Invalid file upload', ['error_code' => $file->getError()]);
        return back()->with('error', 'File upload failed. Error code: ' . $file->getError() . '. Try a smaller file.');
    }

    // STEP 4: Get other parameters
    $client = Client::findOrFail($request->client_id);
    $mapping = $request->filled('mapping_id') ? ImportMapping::find($request->mapping_id) : null;
    $skipDuplicates = in_array('skip_duplicates', $request->options ?? [], true);
    $updateExisting = in_array('update_existing', $request->options ?? [], true);

    Log::info('Import parameters', [
        'client' => $client->company_name,
        'mapping' => $mapping?->mapping_name ?? 'Default Fixed Mapping',
        'skip_duplicates' => $skipDuplicates,
        'update_existing' => $updateExisting
    ]);

    // STEP 5: Try to read the file
    try {
        [$headers, $rows] = $this->readSpreadsheetToArrays($file->getRealPath());

        Log::info('File reading successful', [
            'headers_count' => count($headers),
            'rows_count' => count($rows),
            'headers' => $headers,
            'first_row_sample' => array_slice($rows[0] ?? [], 0, 5)
        ]);

        if (empty($headers) || empty($rows)) {
            return back()->with('error', 'File appears to be empty or has no valid data.');
        }

    } catch (\Throwable $e) {
        Log::error('File reading failed', [
            'error' => $e->getMessage(),
            'file' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ]);
        return back()->with('error', 'Could not read file: ' . $e->getMessage() . '. Check if file is corrupted or too large.');
    }

    // STEP 6: Process rows
    $created = 0; $updated = 0; $skipped = 0; $errors = 0;
    $errorMsgs = [];

    DB::beginTransaction();
    try {
        foreach ($rows as $i => $rawRow) {
            try {
                // Use the appropriate mapping method
                if ($mapping) {
                    $data = $this->mapRowDataDynamic($rawRow, $client->id, $mapping, $i + 2);
                } else {
                    $data = $this->mapRowDataFixed($rawRow, $client->id, $i + 2);
                }

                Log::debug("Row " . ($i + 2) . " mapped data", ['data' => $data]);

                // Validate the mapped data
                $validation = $this->validateTerminalData($data, $i + 2);
                if (!$validation['valid']) {
                    $errors++;
                    $errorMsgs[] = $validation['error'];
                    Log::warning("Row validation failed", [
                        'row' => $i + 2,
                        'error' => $validation['error'],
                        'data' => $data
                    ]);
                    continue;
                }

                // Check for existing terminal
                $existing = PosTerminal::where('terminal_id', $data['terminal_id'])->first();

                if ($existing) {
                    if ($skipDuplicates && !$updateExisting) {
                        $skipped++;
                        continue;
                    }
                    if ($updateExisting) {
                        $existing->fill($data)->save();
                        $updated++;
                        Log::debug("Updated existing terminal", ['terminal_id' => $data['terminal_id']]);
                    } else {
                        $errors++;
                        $errorMsgs[] = "Row " . ($i + 2) . ": Terminal {$data['terminal_id']} already exists";
                    }
                    continue;
                }

                // Create new terminal
                PosTerminal::create($data);
                $created++;
                Log::debug("Created new terminal", ['terminal_id' => $data['terminal_id']]);

            } catch (\Throwable $rowEx) {
                $errors++;
                $errorMsg = "Row " . ($i + 2) . ": " . $rowEx->getMessage();
                $errorMsgs[] = $errorMsg;

                Log::error('Row processing error', [
                    'row' => $i + 2,
                    'error' => $rowEx->getMessage(),
                    'raw_row' => array_slice($rawRow, 0, 10),
                    'stack_trace' => $rowEx->getTraceAsString()
                ]);
            }
        }

        DB::commit();

        Log::info('Import completed successfully', [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'total_processed' => count($rows)
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Import transaction failed', [
            'error' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Import failed during processing: ' . $e->getMessage());
    }

    // STEP 7: Build result message
    $summary = "Import completed — Created: {$created}, Updated: {$updated}, Skipped: {$skipped}, Errors: {$errors}";

    if ($errors > 0) {
        $errorSample = implode(' | ', array_slice($errorMsgs, 0, 3));
        if (count($errorMsgs) > 3) {
            $errorSample .= " | +" . (count($errorMsgs) - 3) . " more errors";
        }
        $summary .= "\n\nError details: " . $errorSample;

        if ($errors > 5) {
            $summary .= "\n\nMany errors detected. Check Laravel logs (storage/logs/laravel.log) for full details.";
        }
    }

    // Determine message type
    if ($errors > 0 && $created == 0) {
        return back()->with('error', $summary);
    } elseif ($errors > $created) {
        return back()->with('error', $summary);
    } else {
        return redirect()->route('pos-terminals.index')->with('success', $summary);
    }
}

/**
 * Enhanced preview with better error handling
 */
public function preview(Request $request)
{
    // Handle large files for preview
    set_time_limit(120);
    ini_set('memory_limit', '512M');

    Log::info('=== PREVIEW REQUEST ===', [
        'has_file' => $request->hasFile('file'),
        'mapping_id' => $request->input('mapping_id'),
        'preview_rows' => $request->input('preview_rows', 5)
    ]);

    try {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'mapping_id' => 'nullable|exists:import_mappings,id',
            'preview_rows' => 'nullable|integer|min:1|max:10',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Preview validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
        ], 422);
    }

    if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid file upload. Check file size and format.'
        ], 422);
    }

    $file = $request->file('file');
    $mapping = $request->filled('mapping_id') ? ImportMapping::find($request->mapping_id) : null;
    $rowsToPreview = (int)($request->preview_rows ?? 5);

    Log::info('Preview file details', [
        'name' => $file->getClientOriginalName(),
        'size' => $file->getSize(),
        'mapping' => $mapping?->mapping_name ?? 'Fixed Default'
    ]);

    try {
        [$headers, $rows] = $this->readSpreadsheetToArrays($file->getRealPath());

        Log::info('Preview file structure', [
            'headers' => $headers,
            'total_rows' => count($rows),
            'columns' => count($headers)
        ]);

        if (empty($headers)) {
            return response()->json([
                'success' => false,
                'message' => 'No headers found in file. Check if file has proper structure.'
            ], 422);
        }

    } catch (\Throwable $e) {
        Log::error('Preview file reading failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Could not read file: ' . $e->getMessage()
        ], 422);
    }

    // Generate preview data
    $preview = [];
    $previewCount = 0;

    foreach ($rows as $i => $rawRow) {
        if ($previewCount >= $rowsToPreview) break;

        try {
            // Use appropriate mapping
            if ($mapping) {
                $mapped = $this->mapRowDataDynamic($rawRow, 1, $mapping, $i + 2); // Use client_id = 1 for preview
            } else {
                $mapped = $this->mapRowDataFixed($rawRow, 1, $i + 2);
            }

            $validation = $this->validateTerminalData($mapped, $i + 2);

            $preview[] = [
                'row_number' => $i + 2,
                'raw_data' => array_slice($rawRow, 0, 8), // Show first 8 columns
                'mapped_data' => $mapped,
                'validation_status' => $validation['valid'] ? 'valid' : 'error',
                'validation_message' => $validation['valid'] ? 'OK' : $validation['error'],
            ];

        } catch (\Throwable $e) {
            $preview[] = [
                'row_number' => $i + 2,
                'raw_data' => array_slice($rawRow, 0, 8),
                'mapped_data' => [],
                'validation_status' => 'error',
                'validation_message' => 'Mapping error: ' . $e->getMessage(),
            ];
        }

        $previewCount++;
    }

    return response()->json([
        'success' => true,
        'mapping_name' => $mapping?->mapping_name ?? 'Fixed Default Mapping',
        'headers' => $headers,
        'preview_data' => $preview,
        'total_rows_in_file' => count($rows),
        'file_analysis' => [
            'columns' => count($headers),
            'estimated_processing_time' => count($rows) > 1000 ? '2-5 minutes' : 'Under 1 minute',
            'file_size' => $file->getSize()
        ]
    ]);
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

            // sample rows
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

    /* ==================== Helpers ==================== */

    /**
     * Read CSV/XLSX/XLS to [headers, rows]
     */
    private function readSpreadsheetToArrays(string $path): array
    {
        $type   = IOFactory::identify($path);
        $reader = IOFactory::createReader($type);

        // Improve CSV handling
        if ($type === 'Csv') {
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setSheetIndex(0);
            // Optional: $reader->setInputEncoding('UTF-8');
        }

        $spreadsheet = $reader->load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $all         = $sheet->toArray(null, false, false, false);

        if (empty($all)) return [[], []];

        $headers = array_map(fn($h) => $this->normalizeHeader((string)($h ?? '')), array_shift($all));
        $rows    = array_map(function ($row) {
            return array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);
        }, $all);

        return [$headers, $rows];
    }

    private function normalizeHeader(string $h): string
    {
        $h = mb_strtolower(trim($h));
        $h = str_replace(['  ', '-', '/', '\\'], ' ', $h);
        $h = preg_replace('/\s+/', ' ', $h);
        return $h;
    }

    /**
     * Default fixed mapping (matches your bank template columns)
     * Uses the same positions you had before.
     */
   /**
 * Flexible fixed mapping that detects CSV format
 */
private function mapRowDataFixed(array $row, int $clientId, int $rowNumber = 0): array
{
    $row = array_pad($row, 50, null);

    Log::debug("Mapping row {$rowNumber}", [
        'row_length' => count($row),
        'first_5_values' => array_slice($row, 0, 5),
        'has_terminal_at_0' => !empty($row[0]),
        'has_terminal_at_1' => !empty($row[1])
    ]);

    // Auto-detect format based on row length and content
    if (count(array_filter($row)) == 17) {
        // Zimbabwe format (17 columns)
        return $this->mapZimbabweFormat($row, $clientId);
    } elseif (count(array_filter($row)) >= 20) {
        // Bank format (20+ columns)
        return $this->mapBankFormat($row, $clientId);
    } else {
        // Try to intelligently map
        return $this->mapIntelligentFormat($row, $clientId);
    }
}

/**
 * Map Zimbabwe CSV format (17 columns)
 */
private function mapZimbabweFormat(array $row, int $clientId): array
{
    Log::debug('Using Zimbabwe format mapping');

    $data = [
        'client_id'              => $clientId,
        'terminal_id'            => $this->cleanValue($row[0]),   // Column 0
        'merchant_name'          => $this->cleanValue($row[2]),   // Column 2
        'merchant_contact_person'=> $this->cleanValue($row[3]),   // Column 3
        'merchant_phone'         => $this->cleanPhoneNumber($row[4]),
        'physical_address'       => $this->cleanValue($row[5]),
        'city'                   => $this->cleanValue($row[6]),
        'province'               => $this->cleanValue($row[7]),
        'business_type'          => $this->cleanValue($row[8]),
        'installation_date'      => $this->parseDate($row[9]),
        'terminal_model'         => $this->cleanValue($row[10]),
        'serial_number'          => $this->cleanValue($row[11]),
        'status'                 => $this->mapStatus($row[12]) ?: 'active',
        'current_status'         => $this->mapStatus($row[14]) ?: 'active',
    ];

    // Store extra Zimbabwe-specific fields
    $extraFields = [];
    if (!empty($this->cleanValue($row[13]))) {
        $extraFields['deployment_status'] = $this->cleanValue($row[13]);
    }
    if (!empty($this->cleanValue($row[15]))) {
        $extraFields['site_contact_person'] = $this->cleanValue($row[15]);
    }
    if (!empty($this->cleanValue($row[16]))) {
        $extraFields['site_contact_number'] = $this->cleanValue($row[16]);
    }

    if (!empty($extraFields)) {
        $data['extra_fields'] = $extraFields;
    }

    return array_filter($data, fn($v) => !is_null($v) && $v !== '');
}

/**
 * Map Bank CSV format (20+ columns)
 */
private function mapBankFormat(array $row, int $clientId): array
{
    Log::debug('Using Bank format mapping');

    $data = [
        'client_id'              => $clientId,
        'terminal_id'            => $this->cleanValue($row[1]),   // Column B
        'merchant_name'          => $this->cleanValue($row[4]),   // Column E
        'business_type'          => $this->cleanValue($row[2]),   // Column C
        'physical_address'       => $this->cleanValue($row[5]),   // Column F
        'city'                   => $this->cleanValue($row[6]),   // Column G
        'province'               => $this->cleanValue($row[7]),   // Column H
        'region'                 => $this->cleanValue($row[9]),   // Column J
        'merchant_phone'         => $this->cleanPhoneNumber($row[8]),
        'merchant_contact_person'=> $this->cleanValue($row[19]),  // Column T
        'terminal_model'         => $this->cleanValue($row[12]),  // Column M
        'serial_number'          => $this->cleanValue($row[13]),  // Column N
        'installation_date'      => $this->parseDate($row[10]),   // Column K
        'status'                 => $this->mapStatus($row[14]) ?: 'active',
        'current_status'         => $this->mapStatus($row[14]) ?: 'active',
        'contract_details'       => $this->buildContractDetailsFixed($row),
    ];

    return array_filter($data, fn($v) => !is_null($v) && $v !== '');
}

/**
 * Intelligent mapping - tries to find the right columns
 */
private function mapIntelligentFormat(array $row, int $clientId): array
{
    Log::debug('Using intelligent format mapping');

    // Look for terminal ID in first few columns
    $terminalId = null;
    $merchantName = null;

    for ($i = 0; $i < min(5, count($row)); $i++) {
        $value = $this->cleanValue($row[$i]);
        if ($value && preg_match('/^[A-Z0-9]{5,}$/i', $value)) {
            $terminalId = $value;
            break;
        }
    }

    // Look for merchant name (usually a longer text field)
    for ($i = 1; $i < min(8, count($row)); $i++) {
        $value = $this->cleanValue($row[$i]);
        if ($value && strlen($value) > 3 && !preg_match('/^\d+$/', $value) && $value !== $terminalId) {
            $merchantName = $value;
            break;
        }
    }

    $data = [
        'client_id'      => $clientId,
        'terminal_id'    => $terminalId,
        'merchant_name'  => $merchantName,
        'status'         => 'active',
        'current_status' => 'active',
    ];

    // Try to map other common fields
    for ($i = 0; $i < count($row); $i++) {
        $value = $this->cleanValue($row[$i]);
        if (!$value) continue;

        // Skip already mapped values
        if ($value === $terminalId || $value === $merchantName) continue;

        // Try to identify other fields by patterns
        if (preg_match('/^\d{10,}$/', $value)) {
            $data['merchant_phone'] = $this->cleanPhoneNumber($value);
        } elseif (preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}/', $value)) {
            $data['installation_date'] = $this->parseDate($value);
        } elseif (in_array(strtolower($value), ['active', 'offline', 'faulty', 'maintenance'])) {
            $data['status'] = $this->mapStatus($value) ?: 'active';
            $data['current_status'] = $data['status'];
        }
    }

    Log::debug('Intelligent mapping result', $data);

    return array_filter($data, fn($v) => !is_null($v) && $v !== '');
}

    /**
     * Dynamic mapping using ImportMapping::column_mappings (0-based indexes)
     */
    private function mapRowDataDynamic(array $row, int $clientId, ImportMapping $mapping, int $rowNumber = 0): array
{
    $row = array_pad($row, 50, null);
    $m   = (array)($mapping->column_mappings ?? []);

    // Your existing standard field mapping code stays the same...
    $data = [
        'client_id' => $clientId,
        'terminal_id' => $this->getValue($row, $m, 'terminal_id'),
        'merchant_name' => $this->getValue($row, $m, 'merchant_name'),
        // ... all your existing fields
    ];

    // NEW: Capture any extra columns that weren't mapped to standard fields
    $extraFields = [];
    $standardFields = ['terminal_id', 'merchant_name', 'business_type', 'physical_address', 'city', 'province', 'region', 'merchant_phone', 'merchant_contact_person', 'terminal_model', 'serial_number', 'installation_date', 'status'];

    foreach ($m as $fieldName => $columnIndex) {
        if (!in_array($fieldName, $standardFields) && isset($row[$columnIndex])) {
            $value = $this->cleanValue($row[$columnIndex]);
            if ($value) {
                $extraFields[$fieldName] = $value;
            }
        }
    }

    if (!empty($extraFields)) {
        $data['extra_fields'] = $extraFields;
    }

    return array_filter($data, fn($v) => !is_null($v) && $v !== '');
}

private function getValue($row, $mappings, $field)
{
    $idx = $mappings[$field] ?? null;
    return ($idx !== null && isset($row[$idx])) ? $this->cleanValue($row[$idx]) : null;
}

    private function buildDynamicContractDetails(array $row, array $mappings): ?string
    {
        $details = [];
        $fields  = ['condition','issues','comments','corrective_action','site_contact','site_phone'];

        foreach ($fields as $f) {
            $idx = $mappings[$f] ?? null;
            if ($idx !== null && isset($row[$idx])) {
                $val = $this->cleanValue($row[$idx]);
                if ($val) $details[] = ucfirst(str_replace('_', ' ', $f)).': '.$val;
            }
        }
        return $details ? implode("\n", $details) : null;
    }

    private function buildContractDetailsFixed(array $row): ?string
    {
        $labels = [
            15 => 'Condition',        // P
            16 => 'Issues',           // Q
            17 => 'Comments',         // R
            18 => 'Corrective Action',// S
            20 => 'Site Phone',       // U
        ];
        $details = [];
        foreach ($labels as $idx => $label) {
            $v = $this->cleanValue($row[$idx] ?? null);
            if ($v) $details[] = "{$label}: {$v}";
        }
        return $details ? implode("\n", $details) : null;
    }

    private function validateTerminalData(array $data, int $rowNumber): array
    {
        if (empty($data['terminal_id'])) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Terminal ID is required"];
        }
        if (empty($data['merchant_name'])) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Merchant name is required"];
        }
        if (strlen((string)$data['terminal_id']) < 3) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Terminal ID too short"];
        }
        if (!empty($data['merchant_email'] ?? null) && !filter_var($data['merchant_email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => "Row {$rowNumber}: Invalid email"];
        }
        return ['valid' => true];
    }

    private function cleanValue($value)
    {
        if ($value === null) return null;
        $v = trim((string)$value);
        if ($v === '') return null;
        $nulls = ['null','n/a','na','-','nil','none','empty'];
        return in_array(mb_strtolower($v), $nulls, true) ? null : $v;
    }

    private function parseDate($v)
    {
        if ($v === null) return null;
        $v = trim((string)$v);
        if ($v === '' || in_array(mb_strtolower($v), ['null','n/a','na','-'], true)) return null;

        // Try common formats
        $fmts = ['Y-m-d','d/m/Y','m/d/Y','d-m-Y','m-d-Y','d-M-Y','d-M-y','d-M','j-M','M-d','Y/m/d','d.m.Y'];
        foreach ($fmts as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $v);
            if ($dt) {
                if (!str_contains($fmt, 'Y') && !str_contains($fmt, 'y')) {
                    $dt->setDate((int)date('Y'), (int)$dt->format('n'), (int)$dt->format('j'));
                }
                return $dt->format('Y-m-d');
            }
        }
        // Fallback
        $ts = strtotime($v);
        return $ts ? date('Y-m-d', $ts) : null;
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
        if ($s === '' || in_array($s, ['null','n/a','na','-'], true)) return 'active';

        $map = [
            'active' => 'active','working' => 'active','online' => 'active','ok' => 'active','good' => 'active','operational' => 'active','up' => 'active','running' => 'active',
            'offline' => 'offline','down' => 'offline','not working' => 'offline','not seen' => 'offline','inactive' => 'offline','disconnected' => 'offline',
            'faulty' => 'faulty','broken' => 'faulty','defective' => 'faulty','error' => 'faulty','damaged' => 'faulty','failed' => 'faulty',
            'maintenance' => 'maintenance','repair' => 'maintenance','service' => 'maintenance','servicing' => 'maintenance','under repair' => 'maintenance','in service' => 'maintenance',
        ];
        return $map[$s] ?? 'active';
    }
}
