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
use App\Http\Controllers\AssetCategoryFieldController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TerminalDeploymentController;
use App\Http\Controllers\SiteVisitController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ReportBuilderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ClientDashboardController;
use App\Http\Controllers\PosTerminalImportController;
use App\Http\Controllers\SystemReportsController;
use Illuminate\Support\Facades\Auth;


// ==============================================
// ROOT ROUTE - REDIRECT TO DASHBOARD
// ==============================================

Route::get('/', function () {
    if (Auth::check()) {
        $employee = Auth::user();

        // If you don't have isActive(), use the column directly:
        // if (! $employee->is_active) { ... }
        if (method_exists($employee, 'isActive') ? ! $employee->isActive() : !($employee->is_active ?? true)) {
            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        return redirect('/dashboard');
    }

    return redirect('/login');
});
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
    // DASHBOARD ROUTES - UPDATED WITH NEW PERMISSIONS
    // ==============================================

    Route::get('/dashboard', function () {
        $employee = Auth::user();

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
    // CLIENT MANAGEMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])
            ->middleware('permission:view_clients,manage_clients,all')
            ->name('index');

        Route::get('/create', [ClientController::class, 'create'])
            ->middleware('permission:manage_clients,all')
            ->name('create');

        Route::post('/', [ClientController::class, 'store'])
            ->middleware('permission:manage_clients,all')
            ->name('store');

        Route::get('/{client}', [ClientController::class, 'show'])
            ->middleware('permission:view_clients,manage_clients,all')
            ->whereNumber('client')
            ->name('show');

        Route::get('/{client}/edit', [ClientController::class, 'edit'])
            ->middleware('permission:manage_clients,all')
            ->whereNumber('client')
            ->name('edit');

        Route::put('/{client}', [ClientController::class, 'update'])
            ->middleware('permission:manage_clients,all')
            ->name('update');

        Route::delete('/{client}', [ClientController::class, 'destroy'])
            ->middleware('permission:manage_clients,all')
            ->name('destroy');
    });

    // API endpoint for client info (used by project form)
    Route::get('/api/clients/{client}/info', [ClientController::class, 'getInfo'])
        ->middleware('permission:view_clients,manage_clients,all')
        ->name('api.clients.info');

    // ==============================================
    // POS TERMINAL ROUTES - UPDATED PERMISSIONS
    // ==============================================
    Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
        // Charts
        Route::get('/chart-data', [PosTerminalController::class, 'getChartData'])
            ->middleware('permission:view_terminals,manage_terminals,all')
            ->name('chart-data');

        // Column Mapping
        Route::get('/column-mapping', [PosTerminalController::class, 'showColumnMapping'])
            ->middleware('permission:manage_terminals,all')
            ->name('column-mapping');

        Route::post('/column-mapping', [PosTerminalController::class, 'storeColumnMapping'])
            ->middleware('permission:manage_terminals,all')
            ->name('store-mapping');

        Route::delete('/column-mapping/{mapping}', [PosTerminalController::class, 'deleteColumnMapping'])
            ->middleware('permission:manage_terminals,all')
            ->name('delete-mapping');

        Route::patch('/column-mapping/{mapping}/toggle', [PosTerminalController::class, 'toggleColumnMapping'])
            ->middleware('permission:manage_terminals,all')
            ->name('toggle-mapping');

        // Import / Preview / Template
        Route::get('/import', [PosTerminalController::class, 'showImport'])
            ->middleware('permission:import_data,manage_terminals,all')
            ->name('import.form');

        Route::post('/import', [PosTerminalImportController::class, 'import'])
            ->middleware('permission:import_data,manage_terminals,all')
            ->name('import');

        Route::post('/preview-import', [PosTerminalImportController::class, 'preview'])
            ->middleware('permission:import_data,manage_terminals,all')
            ->name('preview-import');

        Route::post('/preview-import-enhanced', [PosTerminalImportController::class, 'preview'])
            ->middleware('permission:import_data,manage_terminals,all')
            ->name('preview-import-enhanced');

        Route::get('/download-template', [PosTerminalImportController::class, 'downloadTemplate'])
            ->middleware('permission:import_data,manage_terminals,all')
            ->name('download-template');

        // Export
        Route::get('/export', [PosTerminalController::class, 'export'])
            ->middleware('permission:export_data,manage_terminals,all')
            ->name('export');

        // CRUD routes
        Route::get('/', [PosTerminalController::class, 'index'])
            ->middleware('permission:view_terminals,manage_terminals,all')
            ->name('index');

        Route::get('/create', [PosTerminalController::class, 'create'])
            ->middleware('permission:manage_terminals,all')
            ->name('create');

        Route::post('/', [PosTerminalController::class, 'store'])
            ->middleware('permission:manage_terminals,all')
            ->name('store');

        Route::get('/{posTerminal}', [PosTerminalController::class, 'show'])
            ->middleware('permission:view_terminals,manage_terminals,all')
            ->name('show');

        Route::get('/{posTerminal}/edit', [PosTerminalController::class, 'edit'])
            ->middleware('permission:manage_terminals,all')
            ->name('edit');

        Route::put('/{posTerminal}', [PosTerminalController::class, 'update'])
            ->middleware('permission:manage_terminals,all')
            ->name('update');

        Route::delete('/{posTerminal}', [PosTerminalController::class, 'destroy'])
            ->middleware('permission:manage_terminals,all')
            ->name('destroy');

        Route::patch('/{posTerminal}/status', [PosTerminalController::class, 'updateStatus'])
            ->middleware('permission:manage_terminals,all')
            ->name('update-status');

        Route::prefix('/{posTerminal}')->group(function () {
            Route::post('/tickets', [PosTerminalController::class, 'createTicket'])
                ->middleware('permission:manage_tickets,all')
                ->name('tickets.create');

            Route::post('/services', [PosTerminalController::class, 'scheduleService'])
                ->middleware('permission:assign_jobs,manage_jobs,all')
                ->name('services.create');

            Route::post('/notes', [PosTerminalController::class, 'addNote'])
                ->middleware('permission:manage_terminals,all')
                ->name('notes.create');

            Route::get('/statistics', [PosTerminalController::class, 'getStatistics'])
                ->middleware('permission:view_terminals,manage_terminals,all')
                ->name('statistics');
        });
    });

    // ==============================================
    // JOB ASSIGNMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    // Job management routes (for managers/dispatchers)
    Route::prefix('jobs')->name('jobs.')->middleware('permission:manage_jobs,assign_jobs,all')->group(function () {
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
        ->middleware('permission:view_jobs,manage_jobs,assign_jobs,all')
        ->name('jobs.index');

    Route::get('/jobs/assignments/mine', [JobAssignmentController::class, 'mine'])
        ->middleware('permission:view_jobs')
        ->name('jobs.mine');

    // Full page detail view
    Route::get('/jobs/assignments/{assignment}', [JobAssignmentController::class, 'showPage'])
        ->middleware('permission:view_jobs,manage_jobs,assign_jobs,all')
        ->name('jobs.show');

    // API route for status updates
    Route::put('/api/assignments/{assignmentId}/status', [JobAssignmentController::class, 'updateStatus'])
        ->middleware('permission:view_jobs,manage_jobs,assign_jobs,all');

    // ==============================================
    // TECHNICIAN MANAGEMENT ROUTES
    // ==============================================

    Route::middleware('permission:manage_team,manage_employees,all')->group(function () {
        Route::resource('technicians', TechnicianController::class);
        Route::patch('/technicians/{technician}/availability', [TechnicianController::class, 'updateAvailability'])
            ->name('technicians.update-availability');
    });

    // ==============================================
    // ASSET MANAGEMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('assets')->name('assets.')->group(function () {
        // Main CRUD routes
        Route::get('/', [AssetController::class, 'index'])
            ->middleware('permission:view_assets,manage_assets,all')
            ->name('index');

        Route::get('/create', [AssetController::class, 'create'])
            ->middleware('permission:manage_assets,all')
            ->name('create');

        Route::post('/', [AssetController::class, 'store'])
            ->middleware('permission:manage_assets,all')
            ->name('store');

        Route::get('/{asset}', [AssetController::class, 'show'])
            ->middleware('permission:view_assets,manage_assets,all')
            ->name('show');

        Route::get('/{asset}/edit', [AssetController::class, 'edit'])
            ->middleware('permission:manage_assets,all')
            ->name('edit');

        Route::put('/{asset}', [AssetController::class, 'update'])
            ->middleware('permission:manage_assets,all')
            ->name('update');

        Route::delete('/{asset}', [AssetController::class, 'destroy'])
            ->middleware('permission:manage_assets,all')
            ->name('destroy');

        // Stock management
        Route::post('/{asset}/update-stock', [AssetController::class, 'updateStock'])
            ->middleware('permission:manage_assets,all')
            ->name('update-stock');

        Route::post('/bulk-update-stock', [AssetController::class, 'bulkUpdateStock'])
            ->middleware('permission:bulk_operations,manage_assets,all')
            ->name('bulk-update-stock');

        // Reports and exports
        Route::get('/export/csv', [AssetController::class, 'export'])
            ->middleware('permission:export_data,manage_assets,all')
            ->name('export');

        Route::get('/alerts/low-stock', [AssetController::class, 'lowStockAlerts'])
            ->middleware('permission:view_assets,manage_assets,all')
            ->name('low-stock-alerts');

        Route::get('/{asset}/vehicle-info', [AssetController::class, 'getVehicleInfo'])
            ->middleware('permission:view_assets,manage_assets,all')
            ->name('vehicle-info');

        Route::get('/assignment-report', [AssetController::class, 'assignmentReport'])
            ->middleware('permission:view_reports,manage_assets,all')
            ->name('assignment-report');

        Route::get('/overdue-report', [AssetController::class, 'overdueReport'])
            ->middleware('permission:view_reports,manage_assets,all')
            ->name('overdue-report');

        // Asset assignments
        Route::post('/assign', [AssetController::class, 'assignAsset'])
            ->middleware('permission:manage_assets,all')
            ->name('assign');

        // Legacy routes for compatibility
        Route::get('/internal', function () {
            if (Auth::user()->hasPermission('manage_assets') || Auth::user()->hasPermission('all')) {
                return redirect()->route('assets.index');
            } else {
                return redirect()->route('asset-requests.catalog');
            }
        })->name('internal');

        Route::get('/licenses', function () {
            return view('assets.licenses', ['title' => 'Business Licenses']);
        })->middleware('permission:manage_licenses,manage_assets,all')->name('licenses');
    });

    // ==============================================
    // ASSET ASSIGNMENT ROUTES
    // ==============================================

    Route::prefix('asset-assignments')->name('asset-assignments.')->group(function () {
        Route::get('/{assignment}/data', [AssetController::class, 'getAssignmentData'])
            ->middleware('permission:view_assets,manage_assets,all')
            ->name('data');

        Route::patch('/{assignment}/return', [AssetController::class, 'returnAsset'])
            ->middleware('permission:manage_assets,all')
            ->name('return');

        Route::patch('/{assignment}/transfer', [AssetController::class, 'transferAsset'])
            ->middleware('permission:manage_assets,all')
            ->name('transfer');
    });

    // Additional employee routes for asset assignments
    Route::get('/employees/available', [AssetController::class, 'getAvailableEmployees'])
        ->middleware('permission:view_employees,manage_assets,all')
        ->name('employees.available');

    // ==============================================
    // ASSET REQUEST ROUTES
    // ==============================================

    Route::prefix('asset-requests')->name('asset-requests.')->group(function () {
        Route::get('/catalog', [AssetRequestController::class, 'catalog'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('catalog');

        Route::post('/cart/add/{asset}', [AssetRequestController::class, 'addToCart'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('cart.add');

        Route::get('/cart', [AssetRequestController::class, 'cart'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('cart');

        Route::patch('/cart/{asset}', [AssetRequestController::class, 'updateCart'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('cart.update');

        Route::delete('/cart/{asset}', [AssetRequestController::class, 'removeFromCart'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('cart.remove');

        Route::get('/checkout', [AssetRequestController::class, 'checkout'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('checkout');

        Route::post('/', [AssetRequestController::class, 'store'])
            ->middleware('permission:request_assets,view_own_data')
            ->name('store');

        Route::get('/', [AssetRequestController::class, 'index'])
            ->middleware('permission:view_own_requests,view_own_data')
            ->name('index');

        Route::get('/{assetRequest}', [AssetRequestController::class, 'show'])
            ->middleware('permission:view_own_requests,view_own_data')
            ->name('show');

        Route::patch('/{assetRequest}/cancel', [AssetRequestController::class, 'cancel'])
            ->middleware('permission:view_own_requests,view_own_data')
            ->name('cancel');
    });

    // ==============================================
    // ASSET APPROVAL ROUTES
    // ==============================================

    Route::prefix('asset-approvals')->name('asset-approvals.')->group(function () {
        Route::get('/', [AssetApprovalController::class, 'index'])
            ->middleware('permission:approve_requests,all')
            ->name('index');

        Route::get('/{id}', [AssetApprovalController::class, 'show'])
            ->middleware('permission:approve_requests,all')
            ->name('show');

        Route::post('/{id}/approve', [AssetApprovalController::class, 'approve'])
            ->middleware('permission:approve_requests,all')
            ->name('approve');

        Route::post('/{id}/reject', [AssetApprovalController::class, 'reject'])
            ->middleware('permission:approve_requests,all')
            ->name('reject');

        Route::post('/bulk-action', [AssetApprovalController::class, 'bulkAction'])
            ->middleware('permission:bulk_operations,approve_requests,all')
            ->name('bulk-action');

        Route::get('/stats/data', [AssetApprovalController::class, 'getStats'])
            ->middleware('permission:approve_requests,all')
            ->name('stats');

        Route::get('/export/report', [AssetApprovalController::class, 'exportReport'])
            ->middleware('permission:export_reports,approve_requests,all')
            ->name('export');
    });

    // ==============================================
    // EMPLOYEE MANAGEMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])
            ->middleware('permission:view_employees,manage_employees,all')
            ->name('index');

        Route::get('/create', [EmployeeController::class, 'create'])
            ->middleware('permission:manage_employees,all')
            ->name('create');

        Route::post('/', [EmployeeController::class, 'store'])
            ->middleware('permission:manage_employees,all')
            ->name('store');

        Route::get('/{employee}', [EmployeeController::class, 'show'])
            ->middleware('permission:view_employees,manage_employees,all')
            ->name('show');

        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])
            ->middleware('permission:manage_employees,all')
            ->name('edit');

        Route::put('/{employee}', [EmployeeController::class, 'update'])
            ->middleware('permission:manage_employees,all')
            ->name('update');

        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])
            ->middleware('permission:manage_employees,all')
            ->name('destroy');

        // Quick actions
        Route::patch('/{employee}/role', [EmployeeController::class, 'updateRole'])
            ->middleware('permission:manage_employees,all')
            ->name('update-role');

        Route::patch('/{employee}/status', [EmployeeController::class, 'toggleStatus'])
            ->middleware('permission:manage_employees,all')
            ->name('toggle-status');
    });

    // ==============================================
    // EMPLOYEE PROFILE ROUTES
    // ==============================================

    Route::prefix('profile')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'profile'])
            ->middleware('permission:view_own_data')
            ->name('profile');

        Route::get('/edit', [EmployeeController::class, 'editProfile'])
            ->middleware('permission:view_own_data')
            ->name('edit-profile');

        Route::patch('/update', [EmployeeController::class, 'updateProfile'])
            ->middleware('permission:view_own_data')
            ->name('update-profile');

        Route::patch('/password', [EmployeeController::class, 'updatePassword'])
            ->middleware('permission:view_own_data')
            ->name('update-password');
    });

    // ==============================================
    // ROLE MANAGEMENT ROUTES - UPDATED WITH PERMISSIONS
    // ==============================================

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:manage_roles,all')
            ->name('index');

        Route::get('/create', [RoleController::class, 'create'])
            ->middleware('permission:manage_roles,all')
            ->name('create');

        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:manage_roles,all')
            ->name('store');

        Route::get('/{role}', [RoleController::class, 'show'])
            ->middleware('permission:manage_roles,all')
            ->name('show');

        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:manage_roles,all')
            ->name('edit');

        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:manage_roles,all')
            ->name('update');

        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:manage_roles,all')
            ->name('destroy');

        // Additional actions
        Route::post('/{role}/clone', [RoleController::class, 'clone'])
            ->middleware('permission:manage_roles,all')
            ->name('clone');

        Route::patch('/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->middleware('permission:manage_roles,all')
            ->name('update-permissions');
    });

    // ==============================================
    // BUSINESS LICENSE ROUTES
    // ==============================================

    Route::prefix('business-licenses')->name('business-licenses.')->group(function () {
        Route::get('/', [BusinessLicenseController::class, 'index'])
            ->middleware('permission:manage_licenses,all')
            ->name('index');

        Route::get('/create', [BusinessLicenseController::class, 'create'])
            ->middleware('permission:manage_licenses,all')
            ->name('create');

        Route::post('/', [BusinessLicenseController::class, 'store'])
            ->middleware('permission:manage_licenses,all')
            ->name('store');

        Route::get('/{businessLicense}', [BusinessLicenseController::class, 'show'])
            ->middleware('permission:manage_licenses,all')
            ->name('show');

        Route::get('/{businessLicense}/edit', [BusinessLicenseController::class, 'edit'])
            ->middleware('permission:manage_licenses,all')
            ->name('edit');

        Route::put('/{businessLicense}', [BusinessLicenseController::class, 'update'])
            ->middleware('permission:manage_licenses,all')
            ->name('update');

        Route::delete('/{businessLicense}', [BusinessLicenseController::class, 'destroy'])
            ->middleware('permission:manage_licenses,all')
            ->name('destroy');

        // Special actions
        Route::get('/{businessLicense}/renew', [BusinessLicenseController::class, 'renew'])
            ->middleware('permission:manage_licenses,all')
            ->name('renew');

        Route::post('/{businessLicense}/renew', [BusinessLicenseController::class, 'processRenewal'])
            ->middleware('permission:manage_licenses,all')
            ->name('process-renewal');

        Route::get('/{businessLicense}/download', [BusinessLicenseController::class, 'downloadDocument'])
            ->middleware('permission:manage_licenses,all')
            ->name('download');

        // Reports and views
        Route::get('/reports/expiring', [BusinessLicenseController::class, 'expiring'])
            ->middleware('permission:manage_licenses,all')
            ->name('expiring');

        Route::get('/reports/compliance', [BusinessLicenseController::class, 'compliance'])
            ->middleware('permission:manage_licenses,all')
            ->name('compliance');

        Route::get('/filtered-stats', [BusinessLicenseController::class, 'getFilteredStats'])
            ->middleware('permission:manage_licenses,all')
            ->name('filtered-stats');
    });

    // ==============================================
    // TICKET MANAGEMENT ROUTES
    // ==============================================

    Route::resource('tickets', TicketController::class)->middleware('permission:view_tickets,manage_tickets,all');
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.updateStatus');
    Route::post('tickets/{ticket}/assign', [TicketController::class, 'assignTicket'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.assign');

    // Staged Resolution System Routes
    Route::post('tickets/{ticket}/steps', [TicketController::class, 'addStep'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.addStep');
    Route::patch('tickets/{ticket}/steps/{step}/complete', [TicketController::class, 'completeStep'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.completeStep');
    Route::post('tickets/{ticket}/steps/{step}/transfer', [TicketController::class, 'transferStep'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.transferStep');
    Route::patch('tickets/{ticket}/resolve', [TicketController::class, 'resolveTicket'])
        ->middleware('permission:manage_tickets,all')
        ->name('tickets.resolve');
    Route::get('tickets/{ticket}/audit-trail', [TicketController::class, 'getAuditTrail'])
        ->middleware('permission:view_tickets,manage_tickets,all')
        ->name('tickets.auditTrail');

    // ==============================================
    // DEPLOYMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('deployment')->name('deployment.')->middleware('permission:manage_deployments,all')->group(function () {
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
// Add this route inside your deployment routes group
Route::get('/work-order/{assignment}', [TerminalDeploymentController::class, 'downloadWorkOrder'])
    ->name('download-work-order');
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
    // Add these routes to your existing deployment routes group in web.php



    // Work Orders
    Route::post('/generate-work-orders', [TerminalDeploymentController::class, 'generateWorkOrders'])
        ->name('generate-work-orders');
    Route::get('/work-order/{assignment}', [TerminalDeploymentController::class, 'downloadWorkOrder'])
        ->name('download-work-order');

    // Deployment Finalization
    Route::post('/deploy-all', [TerminalDeploymentController::class, 'deployAllAssignments'])
        ->name('deploy-all');
    Route::post('/finalize-deployment', [TerminalDeploymentController::class, 'finalizeDeployment'])
        ->name('finalize-deployment');

// Add this route in your deployment routes group (around line 380 in your routes file)
Route::get('/work-order/{assignment}', [TerminalDeploymentController::class, 'downloadWorkOrder'])
    ->name('download-work-order');
    // Export
    Route::post('/export-assignments', [TerminalDeploymentController::class, 'exportAssignments'])
        ->name('export-assignments');
    Route::get('/download-export/{filename}', [TerminalDeploymentController::class, 'downloadExport'])
        ->name('download-export');



    });

    // ==============================================
    // SITE VISIT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('site-visits')->name('site_visits.')->group(function () {
        Route::get('/', [SiteVisitController::class, 'index'])
            ->middleware('permission:view_visits,manage_visits,all')
            ->name('index');

        Route::post('/', [SiteVisitController::class, 'storeBatch'])
            ->middleware('permission:manage_visits,all')
            ->name('storeBatch');

        Route::get('/lookup/terminal/{id}', [SiteVisitController::class, 'terminalLookup'])
            ->middleware('permission:view_visits,manage_visits,all')
            ->name('terminalLookup');

        Route::get('/lookup/assignment/{id}', [SiteVisitController::class, 'assignmentLookup'])
            ->middleware('permission:view_visits,manage_visits,all')
            ->name('assignmentLookup');

        Route::get('/edit-terminal', [SiteVisitController::class, 'editTerminal'])
            ->middleware('permission:manage_visits,all')
            ->name('edit_terminal');

        Route::get('/{visit}', [SiteVisitController::class, 'show'])
            ->middleware('permission:view_visits,manage_visits,all')
            ->name('show');

        Route::put('/{visit}', [SiteVisitController::class, 'update'])
            ->middleware('permission:manage_visits,all')
            ->name('update');

        Route::post('/{visit}/attachments', [SiteVisitController::class, 'uploadAttachment'])
            ->middleware('permission:manage_visits,all')
            ->name('attachments');
    });

    // Site visit integration with job assignments
    Route::get('/jobs/assignments/{assignment}/visits', [SiteVisitController::class, 'indexForAssignment'])
        ->middleware('permission:view_jobs,view_visits,all')
        ->name('jobs.assignments.visits');

    Route::get('/api/jobs/assignments/{assignment}/visits', [SiteVisitController::class, 'listJson'])
        ->middleware('permission:view_jobs,view_visits,all')
        ->name('api.jobs.assignments.visits');

    // ==============================================
    // REPORTS ROUTES - UPDATED PERMISSIONS
    // ==============================================

    // Report builder - Open to all authenticated users (outside permission group)
    Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/builder', function () {
            return view('reports.builder', ['title' => 'Report Builder']);
        })->name('builder');
    });

    Route::middleware('permission:view_reports,all')->prefix('reports')->name('reports.')->group(function () {
        // Main reports dashboard
        Route::get('/', [SystemReportsController::class, 'index'])->name('index');

        // System-wide reports
        Route::get('/system', [SystemReportsController::class, 'index'])->name('system');
        Route::get('/system/export', [SystemReportsController::class, 'exportSystemReport'])->name('system.export');
        Route::get('/system/export-csv', [SystemReportsController::class, 'exportCsv'])->name('system.export-csv');

        // Technician Visit Reports
        Route::get('/technician-visits', [TechnicianReportsController::class, 'index'])
            ->middleware('permission:view_technician_visits,all')
            ->name('technician-visits');

        Route::get('/technician-visits/filter', [TechnicianReportsController::class, 'filter'])
            ->middleware('permission:view_technician_visits,all')
            ->name('technician-visits.filter');

        Route::get('/technician-visits/{visit}', [TechnicianReportsController::class, 'show'])
            ->middleware('permission:view_technician_visits,all')
            ->name('technician-visits.show');

        Route::get('/technician-visits/{visit}/photos', [TechnicianReportsController::class, 'getPhotos'])
            ->middleware('permission:view_technician_visits,all')
            ->name('technician-visits.photos');

        Route::get('/technician-visits/{visit}/pdf', [TechnicianReportsController::class, 'generatePDF'])
            ->middleware('permission:view_technician_visits,all')
            ->name('technician-visits.pdf');

        Route::get('/technician-visits/export', [TechnicianReportsController::class, 'export'])
            ->middleware('permission:export_reports,all')
            ->name('technician-visits.export');

        // API endpoints for real-time data
        Route::get('/api/system-health', [SystemReportsController::class, 'getSystemHealth']);
        Route::get('/api/terminal-status', [SystemReportsController::class, 'getTerminalStatus']);
        Route::get('/api/service-metrics', [SystemReportsController::class, 'getServiceMetrics']);
    });

    // ==============================================
    // SETTINGS ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('settings')->name('settings.')->middleware('permission:manage_settings,all')->group(function () {
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

        // Department Management Routes
        Route::get('/departments', [SettingsController::class, 'manageDepartments'])->name('departments.manage');
        Route::post('/departments', [SettingsController::class, 'storeDepartment'])->name('departments.store');
        Route::put('/departments/{department}', [SettingsController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{department}', [SettingsController::class, 'deleteDepartment'])->name('departments.delete');

        // Asset Category Fields Management Routes
        Route::get('/asset-categories/{category}/fields', [AssetCategoryFieldController::class, 'index'])->name('asset-category-fields.index');
        Route::post('/asset-categories/{category}/fields', [AssetCategoryFieldController::class, 'store'])->name('asset-category-fields.store');
        Route::put('/asset-category-fields/{field}', [AssetCategoryFieldController::class, 'update'])->name('asset-category-fields.update');
        Route::delete('/asset-category-fields/{field}', [AssetCategoryFieldController::class, 'destroy'])->name('asset-category-fields.destroy');
        Route::post('/asset-categories/{category}/fields/reorder', [AssetCategoryFieldController::class, 'reorder'])->name('asset-category-fields.reorder');
        Route::put('/asset-categories/{category}/settings', [AssetCategoryFieldController::class, 'updateCategory'])->name('asset-category-fields.update-category');
    });

    // ==============================================
    // DOCUMENT MANAGEMENT ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', function () {
            return view('documents.index', ['title' => 'Documents']);
        })->middleware('permission:view_documents,manage_documents,all')
          ->name('index');

        Route::get('/upload', function () {
            return view('documents.upload', ['title' => 'Upload Document']);
        })->middleware('permission:manage_documents,all')
          ->name('upload');
    });

    // ==============================================
    // CLIENT DASHBOARDS ROUTES
    // ==============================================

    Route::prefix('client-dashboards')->name('client-dashboards.')->group(function () {
        Route::get('/', [ClientDashboardController::class, 'index'])
            ->middleware('permission:view_client_dashboards,all')
            ->name('index');

        Route::get('/{client}', [ClientDashboardController::class, 'show'])
            ->middleware('permission:view_client_dashboards,all')
            ->name('show');

        Route::get('/{client}/export-data', [ClientDashboardController::class, 'exportData'])
            ->middleware('permission:export_data,all')
            ->name('export-data');

        Route::get('/{client}/export-table', [ClientDashboardController::class, 'exportTable'])
            ->middleware('permission:export_data,all')
            ->name('export-table');

        Route::get('/{client}/terminals/create', [ClientDashboardController::class, 'createTerminal'])
            ->middleware('permission:manage_terminals,all')
            ->name('terminals.create');

        Route::post('/{client}/terminals', [ClientDashboardController::class, 'storeTerminal'])
            ->middleware('permission:manage_terminals,all')
            ->name('terminals.store');

        Route::get('/{client}/terminals/{terminal}', [ClientDashboardController::class, 'viewTerminal'])
            ->middleware('permission:view_terminals,all')
            ->name('terminals.show');

        Route::get('/{client}/terminals/{terminal}/edit', [ClientDashboardController::class, 'editTerminal'])
            ->middleware('permission:manage_terminals,all')
            ->name('terminals.edit');

        Route::put('/{client}/terminals/{terminal}', [ClientDashboardController::class, 'updateTerminal'])
            ->middleware('permission:manage_terminals,all')
            ->name('terminals.update');

        Route::get('/{client}/projects/create', [ClientDashboardController::class, 'createProject'])
            ->middleware('permission:manage_deployments,all')
            ->name('projects.create');

        Route::post('/{client}/projects', [ClientDashboardController::class, 'storeProject'])
            ->middleware('permission:manage_deployments,all')
            ->name('projects.store');

        Route::get('/{client}/reports/{reportType}', [ClientDashboardController::class, 'generateReport'])
            ->middleware('permission:view_reports,all')
            ->name('reports');

        Route::get('/{client}/filter-data', [ClientDashboardController::class, 'getFilterData'])
            ->middleware('permission:view_client_dashboards,all')
            ->name('filter-data');
    });

    // ==============================================
    // TECHNICIAN ROUTES - UPDATED PERMISSIONS
    // ==============================================

    Route::middleware('permission:view_jobs')->prefix('technician')->name('technician.')->group(function () {
        Route::get('/jobs', function () {
            return view('technician.jobs', ['title' => 'Job Assignments']);
        })->name('jobs');

        Route::get('/reports', function () {
            return view('technician.reports', ['title' => 'Service Reports']);
        })->middleware('permission:create_reports,view_own_reports')
          ->name('reports');

        Route::get('/schedule', function () {
            return view('technician.schedule', ['title' => 'My Schedule']);
        })->middleware('permission:view_schedule')
          ->name('schedule');

        Route::get('/jobs/assignments/{assignment}/terminal/{terminal}/visits', [SiteVisitController::class, 'listForTerminal'])
            ->middleware('permission:view_visits,all')
            ->name('jobs.assignments.terminal.visits');
    });

    // ==============================================
    // VISIT ROUTES
    // ==============================================
    Route::prefix('visits')->name('visits.')->group(function () {
        Route::get('/', [VisitController::class, 'index'])
            ->middleware('permission:view_visits,all')
            ->name('index');

        Route::get('/{visit}', [VisitController::class, 'show'])
            ->middleware('permission:view_visits,all')
            ->name('show');

        Route::get('/suggest/merchants', [VisitController::class, 'suggestMerchants'])
            ->middleware('permission:view_visits,all')
            ->name('suggest.merchants');

        Route::get('/suggest/employees', [VisitController::class, 'suggestEmployees'])
            ->middleware('permission:view_visits,all')
            ->name('suggest.employees');
    });

    // ==============================================
    // REPORT BUILDER ROUTES - Open to all authenticated users
    // ==============================================

    Route::middleware('auth')->group(function () {
        Route::get('/reports/builder', [ReportBuilderController::class, 'index'])->name('reports.builder');
        Route::post('/reports/run', [ReportBuilderController::class, 'run'])->name('reports.run');
        Route::get('/reports/export/csv', [ReportBuilderController::class, 'exportCsv'])->name('reports.export.csv');
        Route::post('/reports/run-custom', [ReportBuilderController::class, 'runCustom'])->name('reports.run.custom');
        Route::get('/reports/options/clients', [ReportBuilderController::class, 'optClients'])->name('reports.options.clients');
        Route::get('/reports/options/projects', [ReportBuilderController::class, 'optProjects'])->name('reports.options.projects');
        Route::get('/reports/options/regions', [ReportBuilderController::class, 'optRegions'])->name('reports.options.regions');
        Route::get('/reports/options/terminals', [ReportBuilderController::class, 'optTerminals'])->name('reports.options.terminals');
        Route::post('/reports/run-simple', [ReportBuilderController::class, 'runSimple'])->name('reports.run.simple');
    });

    // API endpoints for reports - Open to all authenticated users
    Route::middleware('auth')->prefix('api/report')->group(function () {
        Route::post('/preview', [ReportController::class, 'preview'])->name('api.report.preview');
        Route::post('/export', [ReportController::class, 'export'])->name('api.report.export');
        Route::get('/fields', [ReportController::class, 'getAvailableFields'])->name('api.report.fields');

        // Report template CRUD
        Route::prefix('templates')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportTemplateController::class, 'index'])->name('api.report.templates.index');
            Route::post('/', [\App\Http\Controllers\ReportTemplateController::class, 'store'])->name('api.report.templates.store');
            Route::get('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'show'])->name('api.report.templates.show');
            Route::put('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'update'])->name('api.report.templates.update');
            Route::delete('/{id}', [\App\Http\Controllers\ReportTemplateController::class, 'destroy'])->name('api.report.templates.destroy');
            Route::post('/{id}/duplicate', [\App\Http\Controllers\ReportTemplateController::class, 'duplicate'])->name('api.report.templates.duplicate');
            Route::get('/tags/list', [\App\Http\Controllers\ReportTemplateController::class, 'getTags'])->name('api.report.templates.tags');
        });

        Route::post('/ping', fn () => response()->json(['ok' => true, 'time' => now()]));
    });

    // ==============================================
    // DEBUG ROUTE (Remove in production)
    // ==============================================
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
// PROJECT MANAGEMENT ROUTES (UPDATED FOR CLOSURE SYSTEM)
// ==============================================
Route::prefix('projects')->name('projects.')->middleware('permission:manage_projects,view_projects,all')->group(function () {
    // Basic CRUD routes
    Route::get('/', [App\Http\Controllers\ProjectController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ProjectController::class, 'createImproved'])
        ->middleware('permission:manage_projects,all')
        ->name('create');
    Route::post('/', [App\Http\Controllers\ProjectController::class, 'store'])
        ->middleware('permission:manage_projects,all')
        ->name('store');

    // UPDATED: Closure reports routes (renamed from completion-reports)
    Route::get('/closure-reports', [App\Http\Controllers\ProjectController::class, 'closureReports'])
        ->middleware('permission:manage_projects,all')
        ->name('closure-reports');

    // UPDATED: Generate closure report (renamed from generate-completion-report)
    Route::post('/closure-reports/generate', [App\Http\Controllers\ProjectController::class, 'generateClosureReport'])
        ->middleware('permission:manage_projects,all')
        ->name('generate-closure-report');

    // SPECIFIC ROUTES MUST COME BEFORE GENERIC {project} ROUTE

    // UPDATED: Closure wizard (renamed from completion-wizard)
    Route::get('/{project}/closure-wizard', [App\Http\Controllers\ProjectController::class, 'closureWizard'])
        ->middleware('permission:manage_projects,all')
        ->name('closure-wizard');

    // REMOVED: completion-success (no longer needed in closure flow)
    // Route::get('/{project}/completion-success'...

    Route::get('/{project}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])
        ->middleware('permission:manage_projects,all')
        ->name('edit');

    Route::get('/{project}/download-report', [App\Http\Controllers\ProjectController::class, 'downloadReport'])
        ->name('download-report');

    // MANUAL REPORT GENERATION ROUTES
    Route::get('/{project}/report-generator', [App\Http\Controllers\ProjectController::class, 'showReportGenerator'])
        ->middleware('permission:manage_projects,all')
        ->name('report-generator');

    Route::post('/{project}/generate-reports', [App\Http\Controllers\ProjectController::class, 'generateReports'])
        ->middleware('permission:manage_projects,all')
        ->name('generate-reports');

    // POST routes

    // UPDATED: Close project (renamed from complete)
    Route::post('/{project}/close', [App\Http\Controllers\ProjectController::class, 'close'])
        ->middleware('permission:manage_projects,all')
        ->name('close');

    Route::post('/{project}/regenerate-report', [App\Http\Controllers\ProjectController::class, 'regenerateReport'])
        ->middleware('permission:manage_projects,all')
        ->name('regenerate-report');

    Route::post('/{project}/email-report', [App\Http\Controllers\ProjectController::class, 'emailReport'])
        ->middleware('permission:manage_projects,all')
        ->name('email-report');

    // PUT routes
    Route::put('/{project}', [App\Http\Controllers\ProjectController::class, 'update'])
        ->middleware('permission:manage_projects,all')
        ->name('update');

    // PATCH routes
    Route::patch('/{project}/status', [App\Http\Controllers\ProjectController::class, 'updateStatus'])
        ->middleware('permission:manage_projects,all')
        ->name('update-status');

    // AJAX/API routes
    Route::get('/client/{client}/terminals', [App\Http\Controllers\ProjectController::class, 'getAvailableTerminals'])
        ->name('client.terminals');

    // Bulk operations
    Route::post('/bulk/complete', [App\Http\Controllers\ProjectController::class, 'bulkComplete'])
        ->middleware('permission:manage_projects,all')
        ->name('bulk.complete');
    Route::post('/bulk/export', [App\Http\Controllers\ProjectController::class, 'bulkExport'])
        ->name('bulk.export');

    // GENERIC {project} ROUTE MUST BE LAST!!!
    Route::get('/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('show');
});
});



