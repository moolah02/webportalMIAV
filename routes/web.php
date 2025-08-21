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

// ==============================================
// ROOT ROUTE - SMART REDIRECT
// ==============================================

Route::get('/', function () {
    if (auth()->check()) {
        $employee = auth()->user();
        
        if (!$employee->isActive()) {
            auth()->logout();
            return redirect('/login')->withErrors(['email' => 'Your account has been deactivated.']);
        }
        
        if ($employee->hasPermission('all') || $employee->hasPermission('view_dashboard')) {
            return redirect('/dashboard');
        } elseif ($employee->hasPermission('view_jobs')) {
            return redirect('/technician/dashboard');
        } else {
            return redirect('/employee/dashboard');
        }
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
    
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view_dashboard')
        ->name('dashboard');
    
    Route::get('/technician/dashboard', [DashboardController::class, 'technician'])
        ->middleware('permission:view_jobs')
        ->name('technician.dashboard');
    
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
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    // ==============================================
    // POS TERMINAL ROUTES - CONSOLIDATED
    // ==============================================

    Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
        // SPECIFIC ROUTES FIRST (must come before dynamic routes)
        
        // Chart Data Route
        Route::get('/chart-data', [PosTerminalController::class, 'getChartData'])
            ->middleware('permission:view_terminals,manage_team,all')
            ->name('chart-data');
        
        // Column Mapping Routes
        Route::get('/column-mapping', [PosTerminalController::class, 'showColumnMapping'])
            ->middleware('permission:manage_team,all')
            ->name('column-mapping');
            
        Route::post('/column-mapping', [PosTerminalController::class, 'storeColumnMapping'])
            ->middleware('permission:manage_team,all')
            ->name('store-mapping');
            
        Route::delete('/column-mapping/{mapping}', [PosTerminalController::class, 'deleteColumnMapping'])
            ->middleware('permission:manage_team,all')
            ->name('delete-mapping');
            
        Route::patch('/column-mapping/{mapping}/toggle', [PosTerminalController::class, 'toggleColumnMapping'])
            ->middleware('permission:manage_team,all')
            ->name('toggle-mapping');
        
        // Import/Export Routes
        Route::get('/import', [PosTerminalController::class, 'showImport'])
            ->middleware('permission:manage_team,all')
            ->name('import.form');
            
        Route::post('/import', [PosTerminalController::class, 'import'])
            ->middleware('permission:manage_team,all')
            ->name('import');
            
        Route::post('/preview-import', [PosTerminalController::class, 'previewImport'])
            ->middleware('permission:manage_team,all')
            ->name('preview-import');
            
        Route::post('/preview-import-enhanced', [PosTerminalController::class, 'previewImportEnhanced'])
            ->middleware('permission:manage_team,all')
            ->name('preview-import-enhanced');
            
        Route::get('/download-template', [PosTerminalController::class, 'downloadTemplate'])
            ->middleware('permission:manage_team,all')
            ->name('download-template');
            
        Route::get('/export', [PosTerminalController::class, 'export'])
            ->middleware('permission:view_terminals,manage_team,all')
            ->name('export');
        
        // CRUD Routes (dynamic routes come LAST)
        Route::get('/', [PosTerminalController::class, 'index'])
            ->middleware('permission:view_terminals,manage_team,all')
            ->name('index');
            
        Route::get('/create', [PosTerminalController::class, 'create'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('create');
            
        Route::post('/', [PosTerminalController::class, 'store'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('store');
        
        // Individual terminal routes (MUST be last due to {posTerminal} parameter)
        Route::get('/{posTerminal}', [PosTerminalController::class, 'show'])
            ->middleware('permission:view_terminals,manage_team,all')
            ->name('show');
            
        Route::get('/{posTerminal}/edit', [PosTerminalController::class, 'edit'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('edit');
            
        Route::put('/{posTerminal}', [PosTerminalController::class, 'update'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('update');
            
        Route::delete('/{posTerminal}', [PosTerminalController::class, 'destroy'])
            ->middleware('permission:all')
            ->name('destroy');
            
        Route::patch('/{posTerminal}/status', [PosTerminalController::class, 'updateStatus'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('update-status');

        // Terminal-specific action routes
        Route::prefix('/{posTerminal}')->group(function () {
            Route::post('/tickets', [PosTerminalController::class, 'createTicket'])
                ->middleware('permission:update_terminals,manage_team,all')
                ->name('tickets.create');
                
            Route::post('/services', [PosTerminalController::class, 'scheduleService'])
                ->middleware('permission:update_terminals,manage_team,all')
                ->name('services.create');
                
            Route::post('/notes', [PosTerminalController::class, 'addNote'])
                ->middleware('permission:update_terminals,manage_team,all')
                ->name('notes.create');
                
            Route::get('/reports/{type}', [PosTerminalController::class, 'generateReport'])
                ->middleware('permission:view_terminals,manage_team,all')
                ->name('reports.generate');
                
            Route::get('/statistics', [PosTerminalController::class, 'getStatistics'])
                ->middleware('permission:view_terminals,manage_team,all')
                ->name('statistics');
        });
    });

    // ==============================================
    // JOB ASSIGNMENT ROUTES
    // ==============================================

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
    
    // ==============================================
    // JOB ASSIGNMENT views ROUTES
    // ==============================================


// ALL assignments (managers / dispatch)
Route::get('/job-assignments', [JobAssignmentController::class, 'index'])
    ->middleware('permission:manage_team,all')
    ->name('job-assignments.index');

// MY assignments (technician)
Route::get('/my/job-assignments', [JobAssignmentController::class, 'mine'])
    ->middleware('permission:view_jobs')
    ->name('job-assignments.mine');

// Optional: detail page (both roles can view if they own/have permission)
Route::get('/job-assignments/{assignment}', [JobAssignmentController::class, 'show'])
    ->middleware('permission:manage_team,all|view_jobs')
    ->name('job-assignments.show');


// ALL assignments (manager/dispatch)
Route::get('/job-assignments', [JobAssignmentController::class, 'listAll'])
    ->middleware('permission:manage_team,all')
    ->name('job-assignments.index');

// MY assignments (technician)
Route::get('/my/job-assignments', [JobAssignmentController::class, 'mine'])
    ->middleware('permission:view_jobs')
    ->name('job-assignments.mine');
// Job assignments list pages
Route::middleware(['auth', 'active.employee'])->group(function () {
    Route::get('/jobs/assignments', [\App\Http\Controllers\JobAssignmentController::class, 'listAll'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.index');

    Route::get('/jobs/assignments/mine', [\App\Http\Controllers\JobAssignmentController::class, 'mine'])
        ->middleware('permission:view_jobs')
        ->name('jobs.mine');

    // Optional: full page detail view
    Route::get('/jobs/assignments/{assignment}', [\App\Http\Controllers\JobAssignmentController::class, 'showPage'])
        ->middleware('permission:view_jobs')
        ->name('jobs.show');
});

// Optional detail page (HTML)
Route::get('/job-assignments/{assignment}', [JobAssignmentController::class, 'showPage'])
    ->middleware('permission:manage_team,all|view_jobs')
    ->name('job-assignments.show');

    Route::middleware(['auth','active.employee'])->group(function () {
    // list pages...
    Route::get('/jobs/assignments', [JobAssignmentController::class, 'listAll'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.index');

    Route::get('/jobs/assignments/mine', [JobAssignmentController::class, 'mine'])
        ->middleware('permission:view_jobs')
        ->name('jobs.mine');

    // full-page detail
    Route::get('/jobs/assignments/{assignment}', [JobAssignmentController::class, 'showPage'])
        ->middleware('permission:view_jobs,manage_team,all')
        ->name('jobs.show');
   
Route::put('/api/assignments/{assignmentId}/status', [JobAssignmentController::class, 'updateStatus']);
        
});
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
    });

});

?>