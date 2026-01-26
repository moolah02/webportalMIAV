<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PosTerminal;
use App\Models\ProjectTerminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProjectTerminalUploadController extends Controller
{
    /**
     * Preview terminal upload for create mode (before project exists)
     * Uses client_id from request instead of project
     */
    public function previewForCreate(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200',
                'client_id' => 'required|exists:clients,id',
            ]);

            $clientId = $request->input('client_id');

            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 422);
            }

            $file = $request->file('file');
            $filePath = $file->getRealPath();

            // Read terminal IDs from file
            $fileData = $this->readTerminalData($filePath);

            if (empty($fileData['terminal_ids'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No terminal IDs found in file. Ensure file has a "terminal_id" column.'
                ], 422);
            }

            // For create mode, no existing terminals
            $results = [
                'found' => [],
                'already_assigned' => [],
                'not_found' => [],
            ];

            $processedIds = [];

            foreach ($fileData['rows'] as $row) {
                $terminalId = trim($row['terminal_id'] ?? '');

                if (empty($terminalId) || in_array($terminalId, $processedIds)) {
                    continue;
                }
                $processedIds[] = $terminalId;

                // Find terminal by terminal_id for this client
                $terminal = PosTerminal::where('terminal_id', $terminalId)
                    ->where('client_id', $clientId)
                    ->first();

                if (!$terminal) {
                    $terminalAnyClient = PosTerminal::where('terminal_id', $terminalId)->first();

                    $results['not_found'][] = [
                        'terminal_id' => $terminalId,
                        'reason' => $terminalAnyClient
                            ? 'Terminal belongs to different client'
                            : 'Terminal not found in system',
                        'row_data' => $row,
                        'has_full_data' => $this->hasFullTerminalData($row),
                    ];
                } else {
                    $results['found'][] = [
                        'id' => $terminal->id,
                        'terminal_id' => $terminalId,
                        'merchant_name' => $terminal->merchant_name,
                        'city' => $terminal->city,
                        'region' => $terminal->region,
                        'status' => $terminal->status,
                        'inclusion_reason' => $row['inclusion_reason'] ?? null,
                        'notes' => $row['notes'] ?? null,
                    ];
                }
            }

            $client = \App\Models\Client::find($clientId);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => 'new',
                    'name' => 'New Project',
                    'client' => $client->company_name ?? 'Unknown',
                    'current_terminals' => 0,
                ],
                'summary' => [
                    'total_in_file' => count($processedIds),
                    'can_assign' => count($results['found']),
                    'already_assigned' => 0,
                    'not_found' => count($results['not_found']),
                    'not_found_with_data' => count(array_filter($results['not_found'], fn($t) => $t['has_full_data'])),
                ],
                'results' => $results,
                'headers' => $fileData['headers'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($arr) => implode(', ', $arr), $e->errors()))
            ], 422);
        } catch (\Exception $e) {
            Log::error('Preview for create failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview terminal upload for project
     * Reads CSV/XLSX and categorizes terminals: found, not_found, already_assigned
     */
    public function preview(Request $request, Project $project)
    {
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        Log::info('=== PROJECT TERMINAL PREVIEW STARTED ===', [
            'project_id' => $project->id,
            'project_name' => $project->project_name,
            'has_file' => $request->hasFile('file'),
        ]);

        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200',
            ]);

            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload'
                ], 422);
            }

            $file = $request->file('file');
            $filePath = $file->getRealPath();

            // Read terminal IDs from file
            $fileData = $this->readTerminalData($filePath);

            if (empty($fileData['terminal_ids'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No terminal IDs found in file. Ensure file has a "terminal_id" column.'
                ], 422);
            }

            // Get existing terminals assigned to this project
            $existingInProject = $project->projectTerminals()
                ->where('is_active', true)
                ->pluck('pos_terminal_id')
                ->toArray();

            // Categorize terminals
            $results = [
                'found' => [],
                'already_assigned' => [],
                'not_found' => [],
            ];

            $processedIds = [];

            foreach ($fileData['rows'] as $row) {
                $terminalId = trim($row['terminal_id'] ?? '');

                if (empty($terminalId) || in_array($terminalId, $processedIds)) {
                    continue;
                }
                $processedIds[] = $terminalId;

                // Find terminal by terminal_id for this client
                $terminal = PosTerminal::where('terminal_id', $terminalId)
                    ->where('client_id', $project->client_id)
                    ->first();

                if (!$terminal) {
                    // Check if terminal exists for any client
                    $terminalAnyClient = PosTerminal::where('terminal_id', $terminalId)->first();

                    $results['not_found'][] = [
                        'terminal_id' => $terminalId,
                        'reason' => $terminalAnyClient
                            ? 'Terminal belongs to different client'
                            : 'Terminal not found in system',
                        'row_data' => $row,
                        'has_full_data' => $this->hasFullTerminalData($row),
                    ];
                } elseif (in_array($terminal->id, $existingInProject)) {
                    $results['already_assigned'][] = [
                        'id' => $terminal->id,
                        'terminal_id' => $terminalId,
                        'merchant_name' => $terminal->merchant_name,
                        'city' => $terminal->city,
                    ];
                } else {
                    $results['found'][] = [
                        'id' => $terminal->id,
                        'terminal_id' => $terminalId,
                        'merchant_name' => $terminal->merchant_name,
                        'city' => $terminal->city,
                        'region' => $terminal->region,
                        'status' => $terminal->status,
                        'inclusion_reason' => $row['inclusion_reason'] ?? null,
                        'notes' => $row['notes'] ?? null,
                    ];
                }
            }

            Log::info('Preview completed', [
                'found' => count($results['found']),
                'already_assigned' => count($results['already_assigned']),
                'not_found' => count($results['not_found']),
            ]);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'client' => $project->client->company_name ?? 'Unknown',
                    'current_terminals' => count($existingInProject),
                ],
                'summary' => [
                    'total_in_file' => count($processedIds),
                    'can_assign' => count($results['found']),
                    'already_assigned' => count($results['already_assigned']),
                    'not_found' => count($results['not_found']),
                    'not_found_with_data' => count(array_filter($results['not_found'], fn($t) => $t['has_full_data'])),
                ],
                'results' => $results,
                'headers' => $fileData['headers'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            Log::error('Preview failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process terminal upload and assign to project
     */
    public function upload(Request $request, Project $project)
    {
        set_time_limit(600);
        ini_set('memory_limit', '2048M');

        Log::info('=== PROJECT TERMINAL UPLOAD STARTED ===', [
            'project_id' => $project->id,
            'terminal_count' => count($request->input('terminal_ids', [])),
        ]);

        try {
            $request->validate([
                'terminal_ids' => 'required|array|min:1',
                'terminal_ids.*' => 'integer|exists:pos_terminals,id',
                'inclusion_reason' => 'nullable|string|max:255',
                'create_missing' => 'boolean',
                'missing_terminals' => 'array',
            ]);

            DB::beginTransaction();

            $assigned = 0;
            $skipped = 0;
            $created = 0;
            $errors = [];

            $existingIds = $project->projectTerminals()
                ->where('is_active', true)
                ->pluck('pos_terminal_id')
                ->toArray();

            // Assign existing terminals
            foreach ($request->terminal_ids as $terminalId) {
                if (in_array($terminalId, $existingIds)) {
                    $skipped++;
                    continue;
                }

                try {
                    ProjectTerminal::create([
                        'project_id' => $project->id,
                        'pos_terminal_id' => $terminalId,
                        'included_at' => now(),
                        'inclusion_reason' => $request->inclusion_reason ?? 'Bulk Upload',
                        'is_active' => true,
                        'created_by' => Auth::id(),
                    ]);
                    $assigned++;
                } catch (\Exception $e) {
                    $errors[] = "Terminal ID {$terminalId}: " . $e->getMessage();
                }
            }

            // Create and assign missing terminals if requested
            if ($request->boolean('create_missing') && !empty($request->missing_terminals)) {
                foreach ($request->missing_terminals as $terminalData) {
                    if (empty($terminalData['terminal_id']) || empty($terminalData['merchant_name'])) {
                        continue;
                    }

                    try {
                        // Create the terminal
                        $newTerminal = PosTerminal::create([
                            'terminal_id' => $terminalData['terminal_id'],
                            'merchant_name' => $terminalData['merchant_name'],
                            'merchant_phone' => $terminalData['merchant_phone'] ?? null,
                            'physical_address' => $terminalData['physical_address'] ?? null,
                            'city' => $terminalData['city'] ?? null,
                            'region' => $terminalData['region'] ?? null,
                            'province' => $terminalData['province'] ?? null,
                            'status' => $terminalData['status'] ?? 'active',
                            'client_id' => $project->client_id,
                        ]);

                        // Assign to project
                        ProjectTerminal::create([
                            'project_id' => $project->id,
                            'pos_terminal_id' => $newTerminal->id,
                            'included_at' => now(),
                            'inclusion_reason' => $request->inclusion_reason ?? 'Bulk Upload - New Terminal',
                            'is_active' => true,
                            'created_by' => Auth::id(),
                        ]);

                        $created++;
                        $assigned++;
                    } catch (\Exception $e) {
                        $errors[] = "Creating terminal {$terminalData['terminal_id']}: " . $e->getMessage();
                    }
                }
            }

            // Update project's terminal count
            $project->update([
                'estimated_terminals_count' => $project->projectTerminals()->where('is_active', true)->count(),
            ]);

            DB::commit();

            Log::info('Upload completed', [
                'assigned' => $assigned,
                'created' => $created,
                'skipped' => $skipped,
                'errors' => count($errors),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$assigned} terminals to project.",
                'assigned' => $assigned,
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
                'total_terminals' => $project->projectTerminals()->where('is_active', true)->count(),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove terminal from project
     */
    public function remove(Project $project, $terminalId)
    {
        try {
            $projectTerminal = ProjectTerminal::where('project_id', $project->id)
                ->where('pos_terminal_id', $terminalId)
                ->firstOrFail();

            $projectTerminal->update(['is_active' => false]);

            // Update project terminal count
            $project->update([
                'estimated_terminals_count' => $project->projectTerminals()->where('is_active', true)->count(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terminal removed from project',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove terminal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template for project terminal upload
     */
    public function downloadTemplate(Request $request)
    {
        $simple = $request->boolean('simple', true);

        if ($simple) {
            $headers = ['terminal_id', 'inclusion_reason', 'notes'];
            $filename = 'project_terminals_simple_template_' . date('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($headers) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM
                fputcsv($out, $headers);

                // Sample rows
                fputcsv($out, ['77202134', 'Initial project scope', 'Priority location']);
                fputcsv($out, ['77202135', 'Client request', '']);
                fputcsv($out, ['77202136', 'Status-based selection', 'Requires maintenance check']);

                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        } else {
            // Full template for creating new terminals
            $headers = [
                'terminal_id', 'merchant_name', 'merchant_phone', 'physical_address',
                'city', 'region', 'province', 'status', 'inclusion_reason', 'notes'
            ];
            $filename = 'project_terminals_full_template_' . date('Y-m-d') . '.csv';

            return response()->streamDownload(function () use ($headers) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, $headers);

                fputcsv($out, [
                    '77202134', 'ABC Store', '+27123456789', '123 Main Street',
                    'Johannesburg', 'Gauteng', 'Gauteng', 'active', 'Initial scope', 'VIP client'
                ]);
                fputcsv($out, [
                    '77202135', 'XYZ Shop', '+27987654321', '456 Oak Avenue',
                    'Cape Town', 'Western Cape', 'Western Cape', 'active', 'Client request', ''
                ]);

                fclose($out);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }
    }

    /**
     * Get terminals currently assigned to project
     */
    public function getProjectTerminals(Project $project)
    {
        $terminals = $project->projectTerminals()
            ->where('is_active', true)
            ->with(['posTerminal' => function($query) {
                $query->select('id', 'terminal_id', 'merchant_name', 'city', 'region', 'status');
            }])
            ->get()
            ->map(function ($pt) {
                return [
                    'id' => $pt->pos_terminal_id,
                    'terminal_id' => $pt->posTerminal->terminal_id ?? 'N/A',
                    'merchant_name' => $pt->posTerminal->merchant_name ?? 'N/A',
                    'city' => $pt->posTerminal->city ?? 'N/A',
                    'region' => $pt->posTerminal->region ?? 'N/A',
                    'status' => $pt->posTerminal->status ?? 'N/A',
                    'included_at' => $pt->included_at?->format('Y-m-d H:i'),
                    'inclusion_reason' => $pt->inclusion_reason,
                ];
            });

        return response()->json([
            'success' => true,
            'terminals' => $terminals,
            'count' => $terminals->count(),
        ]);
    }

    // ==============================================
    // PRIVATE HELPER METHODS
    // ==============================================

    /**
     * Read terminal data from uploaded file
     */
    private function readTerminalData(string $filePath): array
    {
        $type = IOFactory::identify($filePath);
        $reader = IOFactory::createReader($type);

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(true);
        }

        if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
        }

        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $allData = $sheet->toArray(null, true, true, false);

        if (empty($allData)) {
            return ['headers' => [], 'terminal_ids' => [], 'rows' => []];
        }

        // Get and normalize headers
        $headers = array_shift($allData);
        $headers = array_map(fn($h) => $this->normalizeHeader($h), $headers);

        // Find terminal_id column
        $terminalIdIndex = $this->findColumnIndex($headers, 'terminal_id');

        if ($terminalIdIndex === null) {
            throw new \Exception('No terminal_id column found. Please ensure your file has a column named "terminal_id", "Terminal ID", or similar.');
        }

        // Map other useful columns
        $columnMap = [
            'terminal_id' => $terminalIdIndex,
            'inclusion_reason' => $this->findColumnIndex($headers, 'inclusion_reason'),
            'notes' => $this->findColumnIndex($headers, 'notes'),
            'merchant_name' => $this->findColumnIndex($headers, 'merchant_name'),
            'merchant_phone' => $this->findColumnIndex($headers, 'merchant_phone'),
            'physical_address' => $this->findColumnIndex($headers, 'physical_address'),
            'city' => $this->findColumnIndex($headers, 'city'),
            'region' => $this->findColumnIndex($headers, 'region'),
            'province' => $this->findColumnIndex($headers, 'province'),
            'status' => $this->findColumnIndex($headers, 'status'),
        ];

        $terminalIds = [];
        $rows = [];

        foreach ($allData as $row) {
            $terminalId = trim($row[$terminalIdIndex] ?? '');
            if (empty($terminalId)) continue;

            $terminalIds[] = $terminalId;

            $rowData = ['terminal_id' => $terminalId];
            foreach ($columnMap as $field => $index) {
                if ($index !== null && isset($row[$index])) {
                    $rowData[$field] = trim($row[$index]);
                }
            }
            $rows[] = $rowData;
        }

        return [
            'headers' => $headers,
            'terminal_ids' => array_unique($terminalIds),
            'rows' => $rows,
        ];
    }

    /**
     * Find column index by various possible names
     */
    private function findColumnIndex(array $headers, string $field): ?int
    {
        $patterns = [
            'terminal_id' => ['terminal_id', 'terminalid', 'terminal id', 'tid', 'terminal number', 'terminal no'],
            'inclusion_reason' => ['inclusion_reason', 'reason', 'inclusion reason'],
            'notes' => ['notes', 'note', 'comments', 'comment'],
            'merchant_name' => ['merchant_name', 'merchant name', 'business name', 'name', 'merchant'],
            'merchant_phone' => ['merchant_phone', 'phone', 'telephone', 'mobile', 'contact number'],
            'physical_address' => ['physical_address', 'address', 'location', 'street'],
            'city' => ['city', 'town'],
            'region' => ['region', 'area', 'zone'],
            'province' => ['province', 'state'],
            'status' => ['status', 'state', 'condition'],
        ];

        $searchPatterns = $patterns[$field] ?? [$field];

        foreach ($headers as $index => $header) {
            $normalized = $this->normalizeHeader($header);
            if (in_array($normalized, $searchPatterns)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Normalize header string
     */
    private function normalizeHeader(?string $header): string
    {
        if ($header === null) return '';
        $header = mb_strtolower(trim($header));
        $header = str_replace(['  ', '-', '/', '\\', '(', ')'], ' ', $header);
        return preg_replace('/\s+/', ' ', trim($header));
    }

    /**
     * Check if row has enough data to create a new terminal
     */
    private function hasFullTerminalData(array $row): bool
    {
        return !empty($row['terminal_id']) && !empty($row['merchant_name']);
    }
}
