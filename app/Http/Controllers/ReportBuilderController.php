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
}
