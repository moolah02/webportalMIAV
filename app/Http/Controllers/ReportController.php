<?php

namespace App\Http\Controllers;

use App\Services\ReportQueryBuilder;
use App\Models\ReportRun;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Exception;

class ReportController extends Controller
{
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

            if (!$user->can('preview-reports')) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }

            $validated = $request->validate([
                'base' => 'required|array',
                'base.table' => 'required|string',
                'joins' => 'sometimes|array',
                'select' => 'required|array|min:1',
                'where' => 'sometimes|array',
                'group_by' => 'sometimes|array',
                'order_by' => 'sometimes|array',
                'limit' => 'sometimes|integer|min:1|max:10000',
            ]);

            $queryData = $this->queryBuilder->buildQuery($validated);
            $results = $queryData['query']->get();

            // Log the report run
            ReportRun::create([
                'user_id' => $user->id,
                'payload' => $validated,
                'result_count' => $results->count(),
                'executed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count(),
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

            if (!$user->can('export-reports')) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }

            $validated = $request->validate([
                'base' => 'required|array',
                'base.table' => 'required|string',
                'select' => 'required|array|min:1',
                'format' => 'required|string|in:csv,xlsx,pdf',
                'filename' => 'sometimes|string'
            ]);

            $validated['download_all'] = true;
            $queryData = $this->queryBuilder->buildQuery($validated);
            $results = $queryData['query']->get();

            // Log the export
            ReportRun::create([
                'user_id' => $user->id,
                'payload' => array_merge($validated, ['action' => 'export']),
                'result_count' => $results->count(),
                'executed_at' => now()
            ]);

            $filename = $validated['filename'] ?? 'report_' . date('Y-m-d_H-i-s');

            switch ($validated['format']) {
                case 'csv':
                    return $this->exportCsv($results, $filename);
                default:
                    throw new Exception('Only CSV export is implemented in this simplified version');
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    private function exportCsv($results, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');

            if ($results->isNotEmpty()) {
                fputcsv($file, array_keys($results->first()->toArray()));

                foreach ($results as $result) {
                    fputcsv($file, array_values($result->toArray()));
                }
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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
