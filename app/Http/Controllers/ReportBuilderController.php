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

        // Report builder is now open to all authenticated users
        // No permission check required

        // Get available fields and filters for the builder
        $fields = $this->queryBuilder->getAvailableFields();
        $filters = $this->queryBuilder->getFilterOptions();

        // Check user capabilities for advanced features
        $canManageTemplates = $user->can('manage-report-templates');
        $canPreviewReports = true; // Allow all users to preview reports
        $canExportReports = true;  // Allow all users to export reports

        return view('reports.builder', compact(
            'fields',
            'filters',
            'canManageTemplates',
            'canPreviewReports',
            'canExportReports'
        ));
    }
}
