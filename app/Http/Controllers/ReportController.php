<?php

namespace App\Http\Controllers;

use App\Services\ReportQueryBuilder;
use App\Models\ReportRun;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class ReportController extends Controller
{
    /**
     * The most rows a format that must be built in memory will attempt.
     * A CSV is streamed and has no such limit.
     */
    private const MAX_FILE_ROWS = 50000;

    private ReportQueryBuilder $queryBuilder;

    public function __construct(ReportQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
       // $this->middleware('auth');
    }

    public function preview(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'base'              => 'required|array',
                'base.table'        => 'required|string',
                'joins'             => 'sometimes|array',
                'select'            => 'required|array|min:1',
                'select.*.expr'     => 'required|string',
                'select.*.as'       => 'sometimes|nullable|string',
                'select.*.aggregate'=> 'sometimes|nullable|string',
                'where'             => 'sometimes|array',
                'where.*.column'    => 'required_with:where|string',
                'where.*.operator'  => 'required_with:where|string',
                'where.*.value'     => 'sometimes|nullable',
                'having'            => 'sometimes|array',
                'having.*.column'   => 'required_with:having|string',
                'having.*.operator' => 'required_with:having|string',
                'having.*.value'    => 'required_with:having|numeric',
                'group_by'          => 'sometimes|array',
                'order_by'          => 'sometimes|array',
                'sort'              => 'sometimes|nullable|string|in:activity,date_desc,date_asc',
                'sort_column'       => 'sometimes|nullable|string',
                'limit'             => 'sometimes|integer|min:1|max:10000',
                'download_all'      => 'sometimes|boolean',
                'visit_state'       => 'sometimes|nullable|string|in:never,visited',
            ]);

            $queryData = $this->queryBuilder->buildQuery($validated);
            $results = $queryData['query']->get();

            // When the preview hit its row limit, tell the page how many rows there really are.
            $total = $results->count();
            $limit = min($validated['limit'] ?? 100, 10000);
            if (empty($validated['download_all']) && $total >= $limit) {
                try {
                    $countQuery = clone $queryData['query'];
                    $countQuery->limit  = null;
                    $countQuery->orders = null;
                    $total = DB::query()->fromSub($countQuery, 'report_rows')->count();
                } catch (\Throwable $e) {
                    $total = null;
                }
            }

            // Log the report run
            ReportRun::create([
                'user_id'      => $user->id,
                'payload'      => $validated,
                'result_count' => $results->count(),
                'executed_at'  => now(),
                'action'       => 'preview',
                'format'       => null,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count(),
                'total' => $total,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function export(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'base'              => 'required|array',
                'base.table'        => 'required|string',
                'joins'             => 'sometimes|array',
                'select'            => 'required|array|min:1',
                'select.*.expr'     => 'required|string',
                'select.*.as'       => 'sometimes|nullable|string',
                'select.*.aggregate'=> 'sometimes|nullable|string',
                'where'             => 'sometimes|array',
                'where.*.column'    => 'required_with:where|string',
                'where.*.operator'  => 'required_with:where|string',
                'where.*.value'     => 'sometimes|nullable',
                'having'            => 'sometimes|array',
                'having.*.column'   => 'required_with:having|string',
                'having.*.operator' => 'required_with:having|string',
                'having.*.value'    => 'required_with:having|numeric',
                'group_by'          => 'sometimes|array',
                'order_by'          => 'sometimes|array',
                'sort'              => 'sometimes|nullable|string|in:activity,date_desc,date_asc',
                'sort_column'       => 'sometimes|nullable|string',
                'limit'             => 'sometimes|integer|min:1|max:100000',
                'format'            => 'required|string|in:csv,xlsx,pdf',
                'title'             => 'sometimes|nullable|string|max:120',
                'filename'          => 'sometimes|string',
                'download_all'      => 'sometimes|boolean',
                'visit_state'       => 'sometimes|nullable|string|in:never,visited',
            ]);

            $validated['download_all'] = true;
            $queryData = $this->queryBuilder->buildQuery($validated);

            // An export has no row limit, so the size of the answer is the
            // caller's choice. A CSV is streamed straight from the database and
            // costs one row of memory at a time; a workbook and a PDF have to
            // hold every row to build a single file, so those are capped and
            // say so instead of running the server out of memory.
            $rowCount = $this->countRows($queryData['query']);

            if ($validated['format'] !== 'csv' && $rowCount !== null && $rowCount > self::MAX_FILE_ROWS) {
                return response()->json([
                    'success' => false,
                    'error'   => sprintf(
                        'That is %s rows, and a %s file is built in memory - the limit is %s. '
                        . 'Narrow the report with a date range or a filter, or export it as CSV, '
                        . 'which has no limit.',
                        number_format($rowCount),
                        strtoupper($validated['format']),
                        number_format(self::MAX_FILE_ROWS)
                    ),
                ], 400);
            }

            $results = $validated['format'] === 'csv' ? null : $queryData['query']->get();

            // Log the export
            ReportRun::create([
                'user_id'      => $user->id,
                'payload'      => $validated,
                'result_count' => $rowCount ?? ($results?->count() ?? 0),
                'executed_at'  => now(),
                'action'       => 'export',
                'format'       => $validated['format'],
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);

            $filename = $validated['filename'] ?? 'report_' . date('Y-m-d_H-i-s');

            switch ($validated['format']) {
                case 'csv':
                    return $this->exportCsv($queryData['query'], $filename);
                case 'xlsx':
                    return $this->exportXlsx($results, $filename, $validated);
                case 'pdf':
                    return $this->exportPdf($results, $filename, $validated);
                default:
                    throw new Exception('Unsupported export format: ' . $validated['format']);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Excel workbook of the same rows. The format was accepted by validation
     * and then refused by the switch, so asking for it only produced an error.
     */
    private function exportXlsx($results, string $filename, array $config)
    {
        @ini_set('memory_limit', '512M');

        $book = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Report');

        $columns = $results->isNotEmpty() ? array_keys((array) $results->first()) : [];
        $rows = [$columns];
        foreach ($results as $result) {
            $rows[] = array_values((array) $result);
        }
        if ($columns) {
            // Strict comparison so a 0 or an empty string is written, not skipped.
            $sheet->fromArray($rows, null, 'A1', true);
            $sheet->freezePane('A2');
            foreach (range(1, count($columns)) as $index) {
                $sheet->getColumnDimensionByColumn($index)->setAutoSize(true);
            }
        }
        $book->getProperties()
            ->setTitle($config['title'] ?? 'MIAV report')
            ->setCreator('Revival Technologies');

        return response()->streamDownload(function () use ($book) {
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save('php://output');
        }, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Streams the rows straight from the database.
     *
     * Takes the query rather than a result set on purpose: ->get() used to
     * load every matching row into memory first, so a wide enough report
     * could exhaust the server before a single byte was sent. cursor() reads
     * one row at a time, so a ten-row export and a million-row export cost
     * the same.
     */
    private function exportCsv($query, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");   // BOM, so Excel reads accents correctly

            $wroteHeader = false;
            foreach ($query->cursor() as $row) {
                $row = (array) $row;

                if (!$wroteHeader) {
                    fputcsv($file, array_keys($row));
                    $wroteHeader = true;
                }

                fputcsv($file, array_values($row));

                // Keep the connection moving on a long export rather than
                // letting the whole file queue up in PHP's output buffer.
                if (connection_aborted()) {
                    break;
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * How many rows this report really has, or null if it cannot be counted.
     * Used to refuse an in-memory format before it is attempted.
     */
    private function countRows($query): ?int
    {
        try {
            $counted = clone $query;
            $counted->limit  = null;
            $counted->orders = null;

            return DB::query()->fromSub($counted, 'report_rows')->count();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function exportPdf($results, string $filename, array $config)
    {
        ini_set('memory_limit', '512M');

        $PDF_ROW_LIMIT = 2000;
        $truncated = $results->count() > $PDF_ROW_LIMIT;
        $results   = $truncated ? $results->take($PDF_ROW_LIMIT) : $results;
        $columns   = $results->isNotEmpty() ? array_keys((array) $results->first()) : [];

        $pdf = Pdf::loadView('reports.builder-pdf', [
            'results'    => $results,
            'columns'    => $columns,
            'filename'   => $filename,
            'baseTable'  => $config['base']['table'] ?? '',
            'title'      => $config['title'] ?? null, // preset / template name
            'filterNotes' => $this->describeFilters($config['where'] ?? []),
            'rowCount'   => $results->count(),
            'truncated'  => $truncated,
            'generatedAt' => now()->format('d M Y, H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Plain-English list of the active filters for the PDF header, so a printed
     * report says what it contains (e.g. "Discovered terminals only").
     */
    private function describeFilters(array $where): array
    {
        $notes = [];
        foreach ($where as $w) {
            $column = $w['column'] ?? '';
            $value  = $w['value'] ?? null;

            if ($column === 'pos_terminals.source') {
                $notes[] = $value === 'field_discovery'
                    ? 'Discovered terminals only (extra work found on site)'
                    : 'Original terminal list only';
            } elseif (($w['operator'] ?? '') === 'between_dates' && is_array($value)) {
                $notes[] = ucwords(str_replace(['_', '.'], [' ', ' - '], $column)) . ': '
                    . (($value['from'] ?? null) ?: 'start') . ' to ' . (($value['to'] ?? null) ?: 'today');
            } elseif (str_ends_with($column, 'client_id') && $value) {
                $name = \Illuminate\Support\Facades\DB::table('clients')->where('id', $value)->value('company_name');
                $notes[] = 'Client: ' . ($name ?? $value);
            } elseif ($value !== null && $value !== '' && !is_array($value)) {
                $notes[] = ucwords(str_replace(['_', '.'], [' ', ' - '], $column)) . ': ' . $value;
            }
        }
        return $notes;
    }

    public function history(Request $request)
    {
        // The builder is open to every employee, so the history is scoped: you
        // see your own runs unless you may view reports generally. It carries
        // other people's names, emails and IP addresses.
        $user = Auth::user();
        $seesAll = $user && ($user->hasPermission('view_reports') || $user->hasPermission('all'));

        $runs = ReportRun::with('user')
            ->when(! $seesAll, fn ($query) => $query->where('user_id', $user?->id))
            ->orderByDesc('executed_at')
            ->paginate(50);

        return view('reports.history', compact('runs', 'seesAll'));
    }

    public function getAvailableFields(): JsonResponse
    {
        try {
            $fields = $this->queryBuilder->getAvailableFields();
            $filters = $this->queryBuilder->getFilterOptions();

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'filters' => $filters
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
