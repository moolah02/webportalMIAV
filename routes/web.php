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
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\JobAssignmentController;
use App\Http\Controllers\TechnicianReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DeploymentPlanningController;

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
        // Read-only client routes (index, show)
        Route::middleware('permission:view_clients,manage_team,all')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        });
        
        // Write client routes (create, edit, delete)
        Route::middleware('permission:manage_team,all')->group(function () {
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('update');
            Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        });
    });
    
    // ==============================================
    // POS TERMINAL ROUTES - FIXED AND CONSOLIDATED
    // ==============================================
    
    Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
        // SPECIFIC ROUTES FIRST (must come before dynamic routes)
        
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
    });
    
    // ==============================================
    // DEPLOYMENT PLANNING ROUTES (NEW)
    // ==============================================
    
    


Route::middleware(['auth','permission:manage_team,all'])->prefix('deployment')->name('deployment.')->group(function() {
    // GET  /deployment                      → index()
    Route::get('/', [DeploymentPlanningController::class,'index'])->name('index');

    // POST /deployment                      → store()
    Route::post('/', [DeploymentPlanningController::class,'store'])->name('store');

    // GET  /deployment/analytics            → analytics()
    Route::get('/analytics', [DeploymentPlanningController::class,'analytics'])->name('analytics');

    // GET  /deployment/export               → export()
    Route::get('/export', [DeploymentPlanningController::class,'export'])->name('export');

    // GET  /deployment/{template}           → show()
    Route::get('/{template}', [DeploymentPlanningController::class,'show'])->name('show');

    // POST /deployment/{template}/deploy    → deploy()
    Route::post('/{template}/deploy', [DeploymentPlanningController::class,'deploy'])->name('deploy');

    // PUT  /deployment/{template}           → update()
    Route::put('/{template}', [DeploymentPlanningController::class,'update'])->name('update');

    // DELETE /deployment/{template}         → destroy()
    Route::delete('/{template}', [DeploymentPlanningController::class,'destroy'])->name('destroy');

    // AJAX: GET /deployment/regions/{region}/terminals → getRegionTerminals()
    Route::get('/regions/{region}/terminals', [DeploymentPlanningController::class,'getRegionTerminals'])
         ->name('regions.terminals');

            // Get cities for client (for city grouping)
    Route::get('/api/clients/{client}/cities', [DeploymentPlanningController::class, 'getCitiesByClient'])
         ->name('api.cities');
    
    // Get addresses for client (for address grouping)  
    Route::get('/api/clients/{client}/addresses', [DeploymentPlanningController::class, 'getAddressesByClient'])
         ->name('api.addresses');
    
    // Get filtered terminals (enhanced version)
    Route::get('/api/terminals', [DeploymentPlanningController::class, 'getFilteredTerminals'])
         ->name('api.terminals');
});


    // ==============================================
    // JOB ASSIGNMENT ROUTES (FIXED)
    // ==============================================
    
    Route::prefix('jobs')->name('jobs.')->middleware('permission:manage_team,all')->group(function () {
        // Main assignment page
        Route::get('/assignment', [JobAssignmentController::class, 'index'])->name('assignment');
        
        // Store new assignment
        Route::post('/assignment', [JobAssignmentController::class, 'store'])->name('assignment.store');
        
        // Get region terminals for AJAX
        Route::get('/regions/{region}/terminals', [JobAssignmentController::class, 'getRegionTerminals'])->name('regions.terminals');
        
        // Assignment management routes
        Route::get('/assignment/{assignment}', [JobAssignmentController::class, 'show'])->name('assignment.show');
        Route::get('/assignment/{assignment}/edit', [JobAssignmentController::class, 'edit'])->name('assignment.edit');
        Route::put('/assignment/{assignment}', [JobAssignmentController::class, 'update'])->name('assignment.update');
        Route::post('/assignment/{assignment}/cancel', [JobAssignmentController::class, 'cancel'])->name('assignment.cancel');
        Route::post('/assignment/{assignment}/status', [JobAssignmentController::class, 'updateStatus'])->name('assignment.updateStatus');
        
        // Export route
        Route::get('/assignment/export', [JobAssignmentController::class, 'export'])->name('assignment.export');
    });

    // ==============================================
    // API ROUTES FOR AJAX CALLS
    // ==============================================
    
    Route::prefix('api')->name('api.')->group(function () {
        // Terminals API for the JavaScript AJAX calls
        Route::get('/terminals', [JobAssignmentController::class, 'getRegionTerminals'])->name('terminals');
        Route::get('/regions/{regionId}/terminals', [JobAssignmentController::class, 'getRegionTerminals']);
    });

    // ==============================================
    // ASSIGNMENT ROUTES (for JavaScript compatibility)
    // ==============================================
    
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/{assignment}', [JobAssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/complete', [JobAssignmentController::class, 'updateStatus'])->name('complete');
        Route::get('/export', [JobAssignmentController::class, 'export'])->name('export');
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
// ASSET MANAGEMENT ROUTES (UPDATED)
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
    
    // AJAX and API routes
    Route::post('/{asset}/update-stock', [AssetController::class, 'updateStock'])->name('update-stock');
    Route::post('/bulk-update-stock', [AssetController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
    Route::get('/export/csv', [AssetController::class, 'export'])->name('export');
    Route::get('/alerts/low-stock', [AssetController::class, 'lowStockAlerts'])->name('low-stock-alerts');
    Route::get('/{asset}/vehicle-info', [AssetController::class, 'getVehicleInfo'])->name('vehicle-info');
    
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
  Route::middleware(['auth'])->group(function () {
    Route::get('/asset-approvals', [AssetApprovalController::class, 'index'])->name('asset-approvals.index');
    Route::get('/asset-approvals/{id}', [AssetApprovalController::class, 'show'])->name('asset-approvals.show');
    Route::post('/asset-approvals/{id}/approve', [AssetApprovalController::class, 'approve'])->name('asset-approvals.approve');
    Route::post('/asset-approvals/{id}/reject', [AssetApprovalController::class, 'reject'])->name('asset-approvals.reject');
    Route::post('/asset-approvals/bulk-action', [AssetApprovalController::class, 'bulkAction'])->name('asset-approvals.bulk-action');
    
    // Optional additional routes
    Route::get('/asset-approvals/stats/data', [AssetApprovalController::class, 'getStats'])->name('asset-approvals.stats');
    Route::get('/asset-approvals/export/report', [AssetApprovalController::class, 'exportReport'])->name('asset-approvals.export');
});



    // ==============================================
   // ASSET ASSIGNMENT ROUTES (NEW)
   // ==============================================

Route::middleware(['auth'])->group(function () {
    
    // Asset assignment management
    Route::post('/assets/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
    Route::patch('/asset-assignments/{assignment}/return', [AssetController::class, 'returnAsset'])->name('asset-assignments.return');
    Route::patch('/asset-assignments/{assignment}/transfer', [AssetController::class, 'transferAsset'])->name('asset-assignments.transfer');
    
    // AJAX routes for asset assignments
    Route::get('/asset-assignments/{assignment}/data', [AssetController::class, 'getAssignmentData'])->name('asset-assignments.data');
    Route::get('/employees/available', [AssetController::class, 'getAvailableEmployees'])->name('employees.available');
    
    // Asset assignment reports
    Route::get('/assets/assignment-report', [AssetController::class, 'assignmentReport'])->name('assets.assignment-report');
    Route::get('/assets/overdue-report', [AssetController::class, 'overdueReport'])->name('assets.overdue-report');
    

Route::middleware(['auth'])->group(function () {
    // Asset assignment data (for Details button)
    Route::get('/asset-assignments/{assignment}/data', [AssetController::class, 'getAssignmentData'])
        ->name('asset-assignments.data');
    
    // Return asset (for Return button)  
    Route::patch('/asset-assignments/{assignment}/return', [AssetController::class, 'returnAsset'])
        ->name('asset-assignments.return');
    
   
    // Asset assignment data (for Details button)
    Route::get('/asset-assignments/{assignment}/data', [AssetController::class, 'getAssignmentData'])
        ->name('asset-assignments.data');
    
    // Return asset (for Return button)  
    Route::patch('/asset-assignments/{assignment}/return', [AssetController::class, 'returnAsset'])
        ->name('asset-assignments.return');
    
    // Transfer asset (for Transfer button)
    Route::patch('/asset-assignments/{assignment}/transfer', [AssetController::class, 'transferAsset'])
        ->name('asset-assignments.transfer');

    // Existing asset assignment route
    Route::post('/assets/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
});
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
    
    // ==============================================
    // TICKET MANAGEMENT ROUTES
    // ==============================================
    
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', function () { 
            return view('tickets.index', ['title' => 'Tickets']); 
        })->name('index');
        
        Route::get('/create', function () { 
            return view('tickets.create', ['title' => 'Create Ticket']); 
        })->name('create');
    });
    
    // ==============================================
    // REPORTS ROUTES (UPDATED)
    // ==============================================
    
    Route::middleware('permission:view_reports,manage_team,all')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () { 
            return view('reports.index', ['title' => 'Reports Dashboard']); 
        })->name('index');
        
        Route::get('/builder', function () { 
            return view('reports.builder', ['title' => 'Report Builder']); 
        })->name('builder');
        
        // NEW: Technician Visit Reports
        Route::get('/technician-visits', [TechnicianReportsController::class, 'index'])->name('technician-visits');
        Route::get('/technician-visits/filter', [TechnicianReportsController::class, 'filter']);
        Route::get('/technician-visits/{visit}', [TechnicianReportsController::class, 'show']);
        Route::get('/technician-visits/{visit}/photos', [TechnicianReportsController::class, 'getPhotos']);
        Route::get('/technician-visits/{visit}/pdf', [TechnicianReportsController::class, 'generatePDF']);
        Route::get('/technician-visits/export', [TechnicianReportsController::class, 'export']);
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
    // EMPLOYEE PROFILE ROUTES
    // ==============================================
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function () { 
            return view('profile.index', ['title' => 'My Profile']); 
        })->name('index');
        
        Route::get('/edit', function () { 
            return view('profile.edit', ['title' => 'Edit Profile']); 
        })->name('edit');
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
      // Column Mapping Management Routes
    // ==============================================
  

    Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
    // Column Mapping
    Route::get('column-mapping', [PosTerminalController::class, 'showColumnMapping'])
        ->name('column-mapping');
    Route::post('column-mapping', [PosTerminalController::class, 'storeColumnMapping'])
        ->name('store-mapping');
    Route::delete('column-mapping/{mapping}', [PosTerminalController::class, 'deleteColumnMapping'])
        ->name('delete-mapping');
    Route::patch('column-mapping/{mapping}/toggle', [PosTerminalController::class, 'toggleColumnMapping'])
        ->name('toggle-mapping');

        Route::get('/pos-terminals/column-mapping', [App\Http\Controllers\PosTerminalController::class, 'showColumnMapping'])->name('pos-terminals.column-mapping');



        
    // Import & Export
    Route::post('preview-import', [PosTerminalController::class, 'previewImport'])
        ->name('preview-import');
    Route::get('download-template', [PosTerminalController::class, 'downloadTemplate'])
        ->name('download-template');
    Route::get('export', [PosTerminalController::class, 'export'])
        ->name('export');
});


    // ==============================================
    // Business License Routes
    // ==============================================

Route::middleware(['auth'])->group(function () {
    // 
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
    });
});
   
});
?>