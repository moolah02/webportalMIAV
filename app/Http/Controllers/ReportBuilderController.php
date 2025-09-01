<?php

namespace App\Http\Controllers;

use App\Services\ReportQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Check permissions
        if (!$user->can('use-report-builder')) {
            abort(403, 'You do not have permission to access the report builder.');
        }

        // Get available fields and filters for the builder
        $fields = $this->queryBuilder->getAvailableFields();
        $filters = $this->queryBuilder->getFilterOptions();

        // Check user capabilities
        $canManageTemplates = $user->can('manage-report-templates');
        $canPreviewReports = $user->can('preview-reports');
        $canExportReports = $user->can('export-reports');

        return view('reports.builder', compact(
            'fields',
            'filters',
            'canManageTemplates',
            'canPreviewReports',
            'canExportReports'
        ));
    }
}
