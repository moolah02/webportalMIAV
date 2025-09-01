<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PosTerminalController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\AssetApprovalController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BusinessLicenseController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\TechnicianReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DeploymentPlanningController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TerminalDeploymentController;
use App\Http\Controllers\SiteVisitController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\PosTerminalImportController;




// ==============================================
// ROOT ROUTE - REDIRECT TO DASHBOARD
// ==============================================

Route::get('/', function () {
    if (auth()->check()) {
        $employee = auth()->user();

        if (!$employee->isActive()) {
            auth()->logout();
            return redirect('/login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Everyone goes to dashboard now
        return redirect('/dashboard');
    }

    return redirect('/login');
})->name('home');

// ==============================================
// GUEST ROUTES (Login/Register)
// ==============================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// ==============================================
// AUTHENTICATED ROUTES
// ==============================================

Route::middleware(['auth', 'active.employee'])->group(function () {

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // ==============================================
    // DASHBOARD ROUTES
    // ==============================================

    Route::get('/dashboard', function () {
        $employee = auth()->user();

        // Check permissions and redirect to appropriate dashboard
        if ($employee->hasPermission('all') || $employee->hasPermission('view_dashboard')) {
            return app(DashboardController::class)->index(); // Main/Admin Dashboard
        } else {
            return app(DashboardController::class)->employee(); // Employee Dashboard (fallback)
        }
    })->name('dashboard');

    Route::get('/employee/dashboard', [DashboardController::class, 'employee'])
        ->middleware('permission:view_own_data')
        ->name('employee.dashboard');

    // ==============================================
    // CLIENT MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}', [ClientController::class, 'show'])
    ->whereNumber('client')
    ->name('show');

Route::get('/{client}/edit', [ClientController::class, 'edit'])
    ->whereNumber('client')
    ->name('edit');

        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

// ==============================================
// POS TERMINAL ROUTES - CONSOLIDATED (fixed)
// ==============================================
Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
    // --- Specific routes first ---

    // Charts
    Route::get('/chart-data', [PosTerminalController::class, 'getChartData'])
        ->middleware('auth') // loosen for now
        ->name('chart-data');

    // Column Mapping
    Route::get('/column-mapping', [PosTerminalController::class, 'showColumnMapping'])
        ->middleware('auth')
        ->name('column-mapping');

    Route::post('/column-mapping', [PosTerminalController::class, 'storeColumnMapping'])
        ->middleware('auth')
        ->name('store-mapping');

    Route::delete('/column-mapping/{mapping}', [PosTerminalController::class, 'deleteColumnMapping'])
        ->middleware('auth')
        ->name('delete-mapping');

    Route::patch('/column-mapping/{mapping}/toggle', [PosTerminalController::class, 'toggleColumnMapping'])
        ->middleware('auth')
        ->name('toggle-mapping');

    // Import / Preview / Template  -> PosTerminalImportController
    Route::get('/import', [PosTerminalController::class, 'showImport'])
        ->middleware('auth')
        ->name('import.form');

    Route::post('/import', [PosTerminalImportController::class, 'import'])
        ->middleware('auth')
        ->name('import');

    Route::post('/preview-import', [PosTerminalImportController::class, 'preview'])
        ->middleware('auth')
        ->name('preview-import');

    // legacy alias (still points to preview)
    Route::post('/preview-import-enhanced', [PosTerminalImportController::class, 'preview'])
        ->middleware('auth')
        ->name('preview-import-enhanced');

    Route::get('/download-template', [PosTerminalImportController::class, 'downloadTemplate'])
        ->middleware('auth')
        ->name('download-template');

    // Export (used by your Blade "Export" button)
    Route::get('/export', [PosTerminalController::class, 'export'])
        ->middleware('auth')
        ->name('export');

    // --- CRUD (dynamic) routes last ---
    Route::get('/', [PosTerminalController::class, 'index'])
        ->middleware('auth')
        ->name('index');

    Route::get('/create', [PosTerminalController::class, 'create'])
        ->middleware('auth')
        ->name('create');

    Route::post('/', [PosTerminalController::class, 'store'])
        ->middleware('auth')
        ->name('store');

    // These MUST be last due to {posTerminal}
    Route::get('/{posTerminal}', [PosTerminalController::class, 'show'])
        ->middleware('auth')
        ->name('show');

    Route::get('/{posTerminal}/edit', [PosTerminalController::class, 'edit'])
        ->middleware('auth')
        ->name('edit');

    Route::put('/{posTerminal}', [PosTerminalController::class, 'update'])
        ->middleware('auth')
        ->name('update');

    Route::delete('/{posTerminal}', [PosTerminalController::class, 'destroy'])
        ->middleware('auth')
        ->name('destroy');

    Route::patch('/{posTerminal}/status', [PosTerminalController::class, 'updateStatus'])
        ->middleware('auth')
        ->name('update-status');

    Route::prefix('/{posTerminal}')->group(function () {
        Route::post('/tickets', [PosTerminalController::class, 'createTicket'])
            ->middleware('auth')
            ->name('tickets.create');

        Route::post('/services', [PosTerminalController::class, 'scheduleService'])
            ->middleware('auth')
            ->name('services.create');

        Route::post('/notes', [PosTerminalController::class, 'addNote'])
            ->middleware('auth')
            ->name('notes.create');

        Route::get('/statistics', [PosTerminalController::class, 'getStatistics'])
            ->middleware('auth')
            ->name('statistics');
    });
});
// Add to your routes file temporarily
Route::get('/debug-upload', function() {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ]);
});

    // ==============================================
    // JOB ASSIGNMENT ROUTES - CLEANED UP
    // ==============================================

    // Job management routes (for managers/dispatchers)
    Route::prefix('jobs')->name('jobs.')->middleware('permission:manage_team,all')->group(function () {
        Route::get('/assignment', [JobAssignmentController::class, 'index'])->name('assignment');
        Route::post('/assignment', [JobAssignmentController::class, 'store'])->name('assignment.store');
        Route::get('/regions/{region}/terminals', [JobAssignmentController::class, 'getRegionTerminals'])->name('regions.terminals');
        Route::get('/assignment/{assignment}', [JobAssignmentController::class, 'show'])->name('assignment.show');
        Route::get('/assignment/{assignment}/edit', [JobAssignmentController::class, 'edit'])->name('assignment.edit');
        Route::put('/assignment/{assignment}', [JobAssignmentController::class, 'update'])->name('assignment.update');
        Route::post('/assignment/{assignment}/cancel', [JobAssignmentController::class, 'cancel'])->name('assignment.cancel');
        Route::post('/assignment/{assignment}/status', [JobAssignmentController::class, 'updateStatus'])->name('assignment.updateStatus');
        Route::get('/assignment/export', [JobAssignmentController::class, 'export'])->name('assignment.export');
    });

    // Job assignments list pages
    Route::get('/jobs/assignments', [JobAssignmentController::class, 'listAll'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.index');

    Route::get('/jobs/assignments/mine', [JobAssignmentController::class, 'mine'])
        ->middleware('permission:view_jobs')
        ->name('jobs.mine');

    // Full page detail view
    Route::get('/jobs/assignments/{assignment}', [JobAssignmentController::class, 'showPage'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.show');

    // API route for status updates
    Route::put('/api/assignments/{assignmentId}/status', [JobAssignmentController::class, 'updateStatus']);

    // ==============================================
    // TECHNICIAN MANAGEMENT ROUTES
    // ==============================================

    Route::middleware('permission:manage_team,all')->group(function () {
        Route::resource('technicians', TechnicianController::class);
        Route::patch('/technicians/{technician}/availability', [TechnicianController::class, 'updateAvailability'])
            ->name('technicians.update-availability');
    });

    // ==============================================
    // ASSET MANAGEMENT ROUTES - CONSOLIDATED
    // ==============================================

    Route::prefix('assets')->name('assets.')->group(function () {
        // Main CRUD routes
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');

        // Stock management
        Route::post('/{asset}/update-stock', [AssetController::class, 'updateStock'])->name('update-stock');
        Route::post('/bulk-update-stock', [AssetController::class, 'bulkUpdateStock'])->name('bulk-update-stock');

        // Reports and exports
        Route::get('/export/csv', [AssetController::class, 'export'])->name('export');
        Route::get('/alerts/low-stock', [AssetController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/{asset}/vehicle-info', [AssetController::class, 'getVehicleInfo'])->name('vehicle-info');
        Route::get('/assignment-report', [AssetController::class, 'assignmentReport'])->name('assignment-report');
        Route::get('/overdue-report', [AssetController::class, 'overdueReport'])->name('overdue-report');

        // Asset assignments
        Route::post('/assign', [AssetController::class, 'assignAsset'])->name('assign');

        // Legacy routes for compatibility
        Route::get('/internal', function () {
            if (auth()->user()->hasPermission('manage_assets') || auth()->user()->hasPermission('all')) {
                return redirect()->route('assets.index');
            } else {
                return redirect()->route('asset-requests.catalog');
            }
        })->name('internal');

        Route::get('/licenses', function () {
            return view('assets.licenses', ['title' => 'Business Licenses']);
        })->middleware('permission:manage_assets,all')->name('licenses');
    });

    // ==============================================
    // ASSET ASSIGNMENT ROUTES - CONSOLIDATED
    // ==============================================

    Route::prefix('asset-assignments')->name('asset-assignments.')->group(function () {
        Route::get('/{assignment}/data', [AssetController::class, 'getAssignmentData'])->name('data');
        Route::patch('/{assignment}/return', [AssetController::class, 'returnAsset'])->name('return');
        Route::patch('/{assignment}/transfer', [AssetController::class, 'transferAsset'])->name('transfer');
    Route::patch('/asset-assignments/{assignment}/transfer', [AssetController::class, 'transferAsset'])
     ->name('asset-assignments.transfer');
    });

    // Additional employee routes for asset assignments
    Route::get('/employees/available', [AssetController::class, 'getAvailableEmployees'])->name('employees.available');

    // ==============================================
    // ASSET REQUEST ROUTES
    // ==============================================

    Route::prefix('asset-requests')->name('asset-requests.')->group(function () {
        Route::get('/catalog', [AssetRequestController::class, 'catalog'])->name('catalog');
        Route::post('/cart/add/{asset}', [AssetRequestController::class, 'addToCart'])->name('cart.add');
        Route::get('/cart', [AssetRequestController::class, 'cart'])->name('cart');
        Route::patch('/cart/{asset}', [AssetRequestController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/{asset}', [AssetRequestController::class, 'removeFromCart'])->name('cart.remove');
        Route::get('/checkout', [AssetRequestController::class, 'checkout'])->name('checkout');
        Route::post('/', [AssetRequestController::class, 'store'])->name('store');
        Route::get('/', [AssetRequestController::class, 'index'])->name('index');
        Route::get('/{assetRequest}', [AssetRequestController::class, 'show'])->name('show');
        Route::patch('/{assetRequest}/cancel', [AssetRequestController::class, 'cancel'])->name('cancel');
    });

    // ==============================================
    // ASSET APPROVAL ROUTES
    // ==============================================

    Route::prefix('asset-approvals')->name('asset-approvals.')->group(function () {
        Route::get('/', [AssetApprovalController::class, 'index'])->name('index');
        Route::get('/{id}', [AssetApprovalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [AssetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AssetApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-action', [AssetApprovalController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/stats/data', [AssetApprovalController::class, 'getStats'])->name('stats');
        Route::get('/export/report', [AssetApprovalController::class, 'exportReport'])->name('export');
    });

    // ==============================================
    // EMPLOYEE MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');

        // Quick actions
        Route::patch('/{employee}/role', [EmployeeController::class, 'updateRole'])->name('update-role');
        Route::patch('/{employee}/status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
    });

    // ==============================================
    // EMPLOYEE PROFILE ROUTES
    // ==============================================

    Route::prefix('profile')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'profile'])->name('profile');
        Route::get('/edit', [EmployeeController::class, 'editProfile'])->name('edit-profile');
        Route::patch('/update', [EmployeeController::class, 'updateProfile'])->name('update-profile');
        Route::patch('/password', [EmployeeController::class, 'updatePassword'])->name('update-password');
    });

    // ==============================================
    // ROLE MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');

        // Additional actions
        Route::post('/{role}/clone', [RoleController::class, 'clone'])->name('clone');
        Route::patch('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('update-permissions');
    });

    // ==============================================
    // BUSINESS LICENSE ROUTES
    // ==============================================

    Route::prefix('business-licenses')->name('business-licenses.')->group(function () {
        Route::get('/', [BusinessLicenseController::class, 'index'])->name('index');
        Route::get('/create', [BusinessLicenseController::class, 'create'])->name('create');
        Route::post('/', [BusinessLicenseController::class, 'store'])->name('store');
        Route::get('/{businessLicense}', [BusinessLicenseController::class, 'show'])->name('show');
        Route::get('/{businessLicense}/edit', [BusinessLicenseController::class, 'edit'])->name('edit');
        Route::put('/{businessLicense}', [BusinessLicenseController::class, 'update'])->name('update');
        Route::delete('/{businessLicense}', [BusinessLicenseController::class, 'destroy'])->name('destroy');

        // Special actions
        Route::get('/{businessLicense}/renew', [BusinessLicenseController::class, 'renew'])->name('renew');
        Route::post('/{businessLicense}/renew', [BusinessLicenseController::class, 'processRenewal'])->name('process-renewal');
        Route::get('/{businessLicense}/download', [BusinessLicenseController::class, 'downloadDocument'])->name('download');

        // Reports and views
        Route::get('/reports/expiring', [BusinessLicenseController::class, 'expiring'])->name('expiring');
        Route::get('/reports/compliance', [BusinessLicenseController::class, 'compliance'])->name('compliance');
        Route::get('/filtered-stats', [BusinessLicenseController::class, 'getFilteredStats'])->name('filtered-stats');
    });

    // ==============================================
    // TICKET MANAGEMENT ROUTES
    // ==============================================

    Route::resource('tickets', TicketController::class);
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assignTicket'])->name('tickets.assign');

    // ==============================================
    // DEPLOYMENT ROUTES - CONSOLIDATED
    // ==============================================

    Route::prefix('deployment')->name('deployment.')->group(function () {
        // Main deployment page
        Route::get('/', [TerminalDeploymentController::class, 'index'])->name('index');
        Route::get('/hierarchical', [TerminalDeploymentController::class, 'index'])->name('hierarchical');

        // AJAX endpoints for UI
        Route::post('/projects', [TerminalDeploymentController::class, 'getProjectsByClients'])->name('projects');
        Route::post('/terminals', [TerminalDeploymentController::class, 'getHierarchicalTerminals'])->name('terminals');
        Route::post('/assign', [TerminalDeploymentController::class, 'createAssignment'])->name('assign');
        Route::post('/bulk-assign', [TerminalDeploymentController::class, 'bulkAssign'])->name('bulk-assign');

        // Project management
        Route::post('/projects/create', [TerminalDeploymentController::class, 'createProject'])->name('projects.create');
        Route::patch('/projects/{project}', [TerminalDeploymentController::class, 'updateProject'])->name('projects.update');

        // Export & work orders
        Route::get('/export/{format}', [TerminalDeploymentController::class, 'exportAssignments'])->name('export');
        Route::post('/work-orders', [TerminalDeploymentController::class, 'generateWorkOrders'])->name('work-orders');
        Route::get('/mobile-sync', [TerminalDeploymentController::class, 'mobileSync'])->name('mobile-sync');

        // Assignment management
        Route::get('/assignments/{assignment}', [TerminalDeploymentController::class, 'getAssignmentDetails'])->name('assignments.show');
        Route::patch('/assignments/{assignment}', [TerminalDeploymentController::class, 'updateAssignment'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [TerminalDeploymentController::class, 'cancelAssignment'])->name('assignments.cancel');
        Route::get('/assigned-terminals', [TerminalDeploymentController::class, 'getAssignedTerminals'])->name('assigned-terminals');

        // Quick actions
        Route::post('/quick-assign', [TerminalDeploymentController::class, 'quickAssignTerminal'])->name('quick-assign');
        Route::post('/auto-assign', [TerminalDeploymentController::class, 'autoAssignTerminals'])->name('auto-assign');
        Route::get('/unassigned', [TerminalDeploymentController::class, 'getUnassignedTerminals'])->name('unassigned');

        // Deployment tracking
        Route::post('/drafts', [TerminalDeploymentController::class, 'saveAsDraft'])->name('drafts.store');
        Route::get('/drafts/{draft}', [TerminalDeploymentController::class, 'loadDraft'])->name('drafts.show');
        Route::post('/deploy', [TerminalDeploymentController::class, 'deployAll'])->name('deploy');
        Route::get('/progress/{deployment}', [TerminalDeploymentController::class, 'getDeploymentProgress'])->name('progress');
        Route::get('/initial-data', [TerminalDeploymentController::class, 'getInitialData'])->name('initial-data');

        // Statistics & reporting
        Route::get('/stats', [TerminalDeploymentController::class, 'getStatistics'])->name('stats');
        Route::get('/technician-workload', [TerminalDeploymentController::class, 'getTechnicianWorkload'])->name('technician-workload');
        Route::get('/regional-summary', [TerminalDeploymentController::class, 'getRegionalSummary'])->name('regional-summary');
    });

    // ==============================================
    // SITE VISIT ROUTES - CONSOLIDATED
    // ==============================================

    Route::prefix('site-visits')->name('site_visits.')->group(function () {
        // Main site visits page (create/batch form + recent visits)
        Route::get('/', [SiteVisitController::class, 'index'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('index');

        // Create one or many visits in one request
        Route::post('/', [SiteVisitController::class, 'storeBatch'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('storeBatch');

        // Lookups (autofill)
        Route::get('/lookup/terminal/{id}', [SiteVisitController::class, 'terminalLookup'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('terminalLookup');

        Route::get('/lookup/assignment/{id}', [SiteVisitController::class, 'assignmentLookup'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('assignmentLookup');

        // Edit terminal page
        Route::get('/edit-terminal', [SiteVisitController::class, 'editTerminal'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('edit_terminal');

        // Single visit view/update
        Route::get('/{visit}', [SiteVisitController::class, 'show'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('show');

        Route::put('/{visit}', [SiteVisitController::class, 'update'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('update');

        // Attachments (photos/signature)
        Route::post('/{visit}/attachments', [SiteVisitController::class, 'uploadAttachment'])
            ->middleware('permission:view_jobs,manage_team,all')
            ->name('attachments');
    });

    // Site visit integration with job assignments
    Route::get('/jobs/assignments/{assignment}/visits', [SiteVisitController::class, 'indexForAssignment'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.assignments.visits');

    // API route for real-time updates
    Route::get('/api/jobs/assignments/{assignment}/visits', [SiteVisitController::class, 'listJson'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('api.jobs.assignments.visits');

    // ==============================================
    // REPORTS ROUTES
    // ==============================================

    Route::middleware('permission:view_reports,manage_team,all')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            return view('reports.index', ['title' => 'Reports Dashboard']);
        })->name('index');

        Route::get('/builder', function () {
            return view('reports.builder', ['title' => 'Report Builder']);
        })->name('builder');

        // Technician Visit Reports
        Route::get('/technician-visits', [TechnicianReportsController::class, 'index'])->name('technician-visits');
        Route::get('/technician-visits/filter', [TechnicianReportsController::class, 'filter']);
        Route::get('/technician-visits/{visit}', [TechnicianReportsController::class, 'show']);
        Route::get('/technician-visits/{visit}/photos', [TechnicianReportsController::class, 'getPhotos']);
        Route::get('/technician-visits/{visit}/pdf', [TechnicianReportsController::class, 'generatePDF']);
        Route::get('/technician-visits/export', [TechnicianReportsController::class, 'export']);
    });

    // ==============================================
    // SETTINGS ROUTES
    // ==============================================

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        // Category Management Routes
        Route::get('/categories/{type}', [SettingsController::class, 'manageCategory'])->name('category.manage');
        Route::post('/categories/{type}', [SettingsController::class, 'storeCategory'])->name('category.store');
        Route::put('/categories/{category}', [SettingsController::class, 'updateCategory'])->name('category.update');
        Route::delete('/categories/{category}', [SettingsController::class, 'deleteCategory'])->name('category.delete');
        Route::post('/categories/reorder', [SettingsController::class, 'updateCategoryOrder'])->name('category.reorder');

        // Role Management Routes
        Route::get('/roles', [SettingsController::class, 'manageRoles'])->name('roles.manage');
        Route::post('/roles', [SettingsController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [SettingsController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [SettingsController::class, 'deleteRole'])->name('roles.delete');
    });

    // ==============================================
    // DOCUMENT MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', function () {
            return view('documents.index', ['title' => 'Documents']);
        })->name('index');

        Route::get('/upload', function () {
            return view('documents.upload', ['title' => 'Upload Document']);
        })->middleware('permission:manage_assets,all')->name('upload');
    });

    // ==============================================
    // ADMIN ROUTES
    // ==============================================

    Route::middleware('permission:all')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/employees', function () {
            return view('admin.employees', ['title' => 'Employee Management']);
        })->name('employees');

        Route::get('/system-settings', function () {
            return view('admin.settings', ['title' => 'System Settings']);
        })->name('settings');

        Route::get('/roles', function () {
            return view('admin.roles', ['title' => 'Role Management']);
        })->name('roles');

        Route::get('/departments', function () {
            return view('admin.departments', ['title' => 'Department Management']);
        })->name('departments');
    });

    // ==============================================
    // MANAGER ROUTES
    // ==============================================

    Route::middleware('permission:manage_team')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/team', function () {
            return view('manager.team', ['title' => 'Team Management']);
        })->name('team');

        Route::get('/approvals', function () {
            return view('manager.approvals', ['title' => 'Pending Approvals']);
        })->name('approvals');

        Route::get('/reports', function () {
            return view('manager.reports', ['title' => 'Team Reports']);
        })->name('reports');
    });

// ==============================================
    // REPORTS ROUTES
    // ==============================================

    Route::middleware(['auth'])->group(function () {
    Route::get('/reports/builder', [ReportBuilderController::class, 'index'])->name('reports.builder');
    Route::post('/reports/run', [ReportBuilderController::class, 'run'])->name('reports.run');
    Route::get('/reports/export/csv', [ReportBuilderController::class, 'exportCsv'])->name('reports.export.csv');
Route::post('/reports/run-custom', [ReportBuilderController::class, 'runCustom'])
    ->name('reports.run.custom');

    Route::post('/reports/run-custom', [ReportBuilderController::class, 'runCustom'])->name('reports.run.custom');

    Route::get('/reports/options/clients',   [ReportBuilderController::class, 'optClients'])->name('reports.options.clients');
Route::get('/reports/options/projects',  [ReportBuilderController::class, 'optProjects'])->name('reports.options.projects');
Route::get('/reports/options/regions',   [ReportBuilderController::class, 'optRegions'])->name('reports.options.regions');
Route::get('/reports/options/terminals', [ReportBuilderController::class, 'optTerminals'])->name('reports.options.terminals');

// Run simple report
Route::post('/reports/run-simple', [ReportBuilderController::class, 'runSimple'])->name('reports.run.simple');
});

    // ==============================================
    // TECHNICIAN ROUTES
    // ==============================================

    Route::middleware('permission:view_jobs')->prefix('technician')->name('technician.')->group(function () {
        Route::get('/jobs', function () {
            return view('technician.jobs', ['title' => 'Job Assignments']);
        })->name('jobs');

        Route::get('/reports', function () {
            return view('technician.reports', ['title' => 'Service Reports']);
        })->name('reports');

        Route::get('/schedule', function () {
            return view('technician.schedule', ['title' => 'My Schedule']);
        })->name('schedule');

        // List visits for an assignment filtered to ONE terminal (View more)
        Route::get(
            '/jobs/assignments/{assignment}/terminal/{terminal}/visits',
            [SiteVisitController::class, 'listForTerminal']
        )->middleware('permission:view_jobs,manage_team,all')
         ->name('jobs.assignments.terminal.visits');
    });
Route::get('/visits', [VisitController::class, 'index'])
    ->name('visits.index');     // <-- this name is what the menu uses

Route::get('/visits/{visit}', [VisitController::class, 'show'])
    ->name('visits.show');
Route::get('/visits/suggest/merchants', [VisitController::class, 'suggestMerchants'])
    ->name('visits.suggest.merchants');
Route::get('/visits/suggest/employees', [VisitController::class, 'suggestEmployees'])
    ->name('visits.suggest.employees');

// ==============================================
// REPORT BUILDER ROUTES
// ==============================================

// UI (Blade page)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/builder', [\App\Http\Controllers\ReportBuilderController::class, 'index'])
        ->name('reports.builder');
});

// API endpoints (session + CSRF, no Sanctum)
Route::middleware(['web', 'auth'])->prefix('api/report')->group(function () {
    Route::post('/preview', [\App\Http\Controllers\ReportController::class, 'preview'])
        ->name('api.report.preview');
    Route::post('/export', [\App\Http\Controllers\ReportController::class, 'export'])
        ->name('api.report.export');
    Route::get('/fields', [\App\Http\Controllers\ReportController::class, 'getAvailableFields'])
        ->name('api.report.fields');

    // Report template CRUD
    Route::prefix('templates')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportTemplateController::class, 'index'])
            ->name('api.report.templates.index');
        Route::post('/', [\App\Http\Controllers\ReportTemplateController::class, 'store'])
            ->name('api.report.templates.store');
        Route::get('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'show'])
            ->name('api.report.templates.show');
        Route::put('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'update'])
            ->name('api.report.templates.update');
        Route::delete('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'destroy'])
            ->name('api.report.templates.destroy');
        Route::post('/{id}/duplicate', [\App\Http\Controllers\ReportTemplateController::class, 'duplicate'])
            ->name('api.report.templates.duplicate');
        Route::get('/tags/list', [\App\Http\Controllers\ReportTemplateController::class, 'getTags'])
            ->name('api.report.templates.tags');
    });

    // Optional: quick test route to verify JSON works
    Route::post('/ping', fn () => response()->json(['ok' => true, 'time' => now()]));
});

// ==============================================
// CLIENT DASHBOARDS ROUTES
// ==============================================

Route::prefix('client-dashboards')->name('client-dashboards.')->group(function () {
    Route::get('/', [ClientDashboardController::class, 'index'])
        ->middleware('permission:view_clients,manage_team,all')
        ->name('index');

    Route::get('/{client}', [ClientDashboardController::class, 'show'])
        ->middleware('permission:view_clients,manage_team,all')
        ->name('show');
});
// Add this to your routes file if you want project management
Route::resource('projects', ProjectController::class)
    ->middleware('permission:manage_team,all');
});
