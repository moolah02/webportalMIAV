<?php

namespace App\Http\Controllers;

use App\Services\ReportQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportBuilderController extends Controller
{
    private ReportQueryBuilder $queryBuilder;

    public function __construct(ReportQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Report builder is now open to all authenticated users
        // No permission check required

        // Get available fields and filters for the builder
        $fields = $this->queryBuilder->getAvailableFields();
        $filters = $this->queryBuilder->getFilterOptions();

        // All authenticated users can save/load their own templates.
        // Only admins can mark a template as global (visible to all users).
        $canManageTemplates  = true;
        $canMakeGlobal       = $user->isAdmin() || $user->hasPermission('manage-report-templates');
        $canPreviewReports   = true;
        $canExportReports    = true;

        return view('reports.builder', compact(
            'fields',
            'filters',
            'canManageTemplates',
            'canMakeGlobal',
            'canPreviewReports',
            'canExportReports'
        ));
    }

    // ── Option lists (read-only JSON) ────────────────────────────────
    // GET /reports/options/{clients|projects|regions|terminals}
    // Every parameter is optional; bad input gets a 422, never a 500.
    // Shape: { success: true, data: [ { id, name, ... } ] }

    public function optClients(Request $request): JsonResponse
    {
        if ($error = $this->invalidOptions($request)) {
            return $error;
        }

        $rows = DB::table('clients')
            ->select('id', 'company_name as name', 'status')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('company_name', 'like', '%' . $request->input('q') . '%'))
            ->orderBy('company_name')
            ->limit($this->optionLimit($request))
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function optProjects(Request $request): JsonResponse
    {
        if ($error = $this->invalidOptions($request)) {
            return $error;
        }

        $rows = DB::table('projects')
            ->select('id', 'project_name as name', 'project_code as code', 'client_id', 'status')
            ->when($request->filled('client_id'), fn ($q) => $q->where('client_id', (int) $request->input('client_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('project_name', 'like', '%' . $request->input('q') . '%'))
            ->orderBy('project_name')
            ->limit($this->optionLimit($request))
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function optRegions(Request $request): JsonResponse
    {
        if ($error = $this->invalidOptions($request)) {
            return $error;
        }

        $rows = DB::table('regions')
            ->select('id', 'name')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%' . $request->input('q') . '%'))
            ->orderBy('name')
            ->limit($this->optionLimit($request))
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    public function optTerminals(Request $request): JsonResponse
    {
        if ($error = $this->invalidOptions($request)) {
            return $error;
        }

        $rows = DB::table('pos_terminals')
            ->select('id', 'terminal_id as name', 'merchant_name', 'client_id', 'region')
            ->when($request->filled('client_id'), fn ($q) => $q->where('client_id', (int) $request->input('client_id')))
            ->when($request->filled('region'), fn ($q) => $q->where('region', $request->input('region')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $q->where(fn ($w) => $w->where('terminal_id', 'like', $term)->orWhere('merchant_name', 'like', $term));
            })
            ->orderBy('terminal_id')
            ->limit($this->optionLimit($request, 200))
            ->get();

        return response()->json(['success' => true, 'data' => $rows]);
    }

    private function invalidOptions(Request $request): ?JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'q'         => 'nullable|string|max:100',
            'status'    => 'nullable|string|max:50',
            'region'    => 'nullable|string|max:100',
            'client_id' => 'nullable|integer|min:1',
            'limit'     => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return null;
    }

    private function optionLimit(Request $request, int $default = 500): int
    {
        return (int) ($request->query('limit') ?: $default);
    }
}
