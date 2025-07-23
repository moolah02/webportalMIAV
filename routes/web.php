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
    
    // Dashboard routes based on permissions
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
    //// ==============================================
// CLIENT MANAGEMENT ROUTES - FIXED VERSION
// Replace your current client routes with this:
// ==============================================

// Client routes with proper middleware separation
Route::middleware(['auth', 'active.employee'])->group(function () {
    
    // Read-only client routes (index, show) - accessible to more roles
    Route::middleware('permission:view_clients,manage_team,all')->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    });
    
    // Write client routes (create, edit, delete) - restricted to managers and admins
    Route::middleware('permission:manage_team,all')->group(function () {
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });
});
    

    
    // ==============================================
    // POS TERMINAL ROUTES
    // ==============================================
    Route::prefix('pos-terminals')->name('pos-terminals.')->group(function () {
        Route::get('/', [PosTerminalController::class, 'index'])
            ->middleware('permission:view_terminals,manage_team,all')
            ->name('index');
            
        Route::get('/create', [PosTerminalController::class, 'create'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('create');
            
        Route::post('/', [PosTerminalController::class, 'store'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('store');
            
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
            
        // Status update route
        Route::patch('/{posTerminal}/status', [PosTerminalController::class, 'updateStatus'])
            ->middleware('permission:update_terminals,manage_team,all')
            ->name('update-status');
            
        // Import routes
        Route::get('/import/show', [PosTerminalController::class, 'showImport'])
            ->middleware('permission:manage_team,all')
            ->name('import.show');
            
        Route::post('/import/process', [PosTerminalController::class, 'import'])
            ->middleware('permission:manage_team,all')
            ->name('import.process');
    });
    
    // ==============================================
    // TECHNICIAN MANAGEMENT ROUTES
    // ==============================================
    Route::middleware('permission:manage_team,all')->group(function () {
        Route::resource('technicians', TechnicianController::class);
        
        // Availability update route
        Route::patch('/technicians/{technician}/availability', [TechnicianController::class, 'updateAvailability'])
            ->name('technicians.update-availability');
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
    // ASSET MANAGEMENT ROUTES (Simplified for now)
    // ==============================================
    Route::prefix('assets')->name('assets.')->group(function () {
        // Internal Assets - accessible to those with manage_assets or view_own_data permission
        Route::get('/internal', function () { 
            return view('assets.internal', ['title' => 'Internal Assets']); 
        })->middleware('permission:manage_assets,view_own_data')->name('internal');
        
     Route::middleware(['auth', 'active.employee'])->group(function () {
    // Existing routes...
    
    // Asset Management Routes
    Route::resource('assets', AssetController::class);
    Route::get('assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{asset}/assign', [AssetController::class, 'assignStore'])->name('assets.assign.store');
    Route::get('assets/{asset}/history', [AssetController::class, 'history'])->name('assets.history');
    
Route::middleware(['auth', 'active.employee'])->group(function () {
    // Asset Management Routes
    Route::resource('assets', AssetController::class);
    Route::post('assets/bulk-update-stock', [AssetController::class, 'bulkUpdateStock'])->name('assets.bulk-update-stock');
    Route::get('assets/export', [AssetController::class, 'export'])->name('assets.export');
    Route::get('assets/low-stock-alerts', [AssetController::class, 'lowStockAlerts'])->name('assets.low-stock-alerts');
    
    // Other existing routes...
});

    // Asset Categories
    Route::get('assets/categories/manage', [AssetController::class, 'categories'])->name('assets.categories');
    
    // Quick asset actions
    Route::patch('assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.status');
});

        // Asset Requests - accessible to all employees
        Route::get('/requests', function () { 
            return view('assets.requests', ['title' => 'Asset Requests']); 
        })->middleware('permission:view_own_data,request_assets,all')->name('requests');
        
        // License Management - admin and managers only
        Route::get('/licenses', function () { 
            return view('assets.licenses', ['title' => 'Business Licenses']); 
        })->middleware('permission:manage_assets,all')->name('licenses');
    });
    
    Route::get('/asset-requests/catalog', [AssetRequestController::class, 'catalog'])->name('asset-requests.catalog');
Route::get('/asset-requests/cart', [AssetRequestController::class, 'cart'])->name('asset-requests.cart');
Route::post('/asset-requests/cart/add/{asset}', [AssetRequestController::class, 'addToCart'])->name('asset-requests.cart.add');
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
    // REPORTS ROUTES
    // ==============================================
    Route::middleware('permission:view_reports,manage_team,all')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () { 
            return view('reports.index', ['title' => 'Reports Dashboard']); 
        })->name('index');
        
        Route::get('/builder', function () { 
            return view('reports.builder', ['title' => 'Report Builder']); 
        })->name('builder');
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
});

// ==============================================
// ROOT ROUTE - SMART REDIRECT
// ==============================================

Route::get('/', function () {
    // If user is authenticated, redirect to appropriate dashboard
    if (auth()->check()) {
        $employee = auth()->user();
        
        // Check if employee is active
        if (!$employee->isActive()) {
            auth()->logout();
            return redirect('/login')->withErrors(['email' => 'Your account has been deactivated.']);
        }
        
        // Redirect based on permissions
        if ($employee->hasPermission('all') || $employee->hasPermission('view_dashboard')) {
            return redirect('/dashboard');
        } elseif ($employee->hasPermission('view_jobs')) {
            return redirect('/technician/dashboard');
        } else {
            return redirect('/employee/dashboard');
        }
    }
    
    // If not authenticated, redirect to login
    return redirect('/login');
})->name('home');

// ==============================================
// FALLBACK ROUTES



// ==============================================
// ASSET SYSTEM ROUTES
// Add these to your routes/web.php file
// ==============================================

Route::middleware(['auth', 'active.employee'])->group(function () {
    
    Route::middleware(['auth', 'active.employee'])->group(function () {
    
    // ==============================================
    // ASSET MANAGEMENT ROUTES (Main Routes)
    // ==============================================
   // Asset Management Routes - Add this to your routes/web.php
Route::middleware(['auth', 'active.employee'])->prefix('assets')->name('assets.')->group(function () {
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::get('/create', [AssetController::class, 'create'])->name('create');
    Route::post('/', [AssetController::class, 'store'])->name('store');
    Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
    
    // Additional asset routes
    Route::post('/bulk-update-stock', [AssetController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
    Route::get('/export/csv', [AssetController::class, 'export'])->name('export');
    Route::get('/alerts/low-stock', [AssetController::class, 'lowStockAlerts'])->name('low-stock-alerts');
});
    
    // ==============================================
    // ASSET REQUEST ROUTES (Employee Shopping Cart)
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
        Route::get('/{assetRequest}', [AssetApprovalController::class, 'show'])->name('show');
        Route::post('/{assetRequest}/approve', [AssetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{assetRequest}/reject', [AssetApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-action', [AssetApprovalController::class, 'bulkAction'])->name('bulk-action');
    });
});
    // ==============================================
    // QUICK ACCESS ROUTES (Update existing asset routes)
    // ==============================================
    
    // Update the existing asset routes to point to new system
    Route::get('/assets/internal', function () {
        // Redirect based on permissions
        if (auth()->user()->hasPermission('manage_assets') || auth()->user()->hasPermission('all')) {
            return redirect()->route('assets.index');
        } else {
            return redirect()->route('asset-requests.catalog');
        }
    })->name('assets.internal');
    
    Route::get('/assets/requests', [AssetRequestController::class, 'index'])->name('assets.requests');
});





// ==============================================
// Client Management Routes (We'll create these next)
// ==============================================
    


Route::middleware(['auth'])->prefix('clients')->name('clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/create', [ClientController::class, 'create'])->name('create');
    Route::post('/', [ClientController::class, 'store'])->name('store');
    Route::get('/{client}', [ClientController::class, 'show'])->name('show');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
    Route::put('/{client}', [ClientController::class, 'update'])->name('update');
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
});



Route::middleware(['auth'])->prefix('clients')->name('clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/create', [ClientController::class, 'create'])->name('create');
    Route::post('/', [ClientController::class, 'store'])->name('store');
    Route::get('/{client}', [ClientController::class, 'show'])->name('show');
    Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
    Route::put('/{client}', [ClientController::class, 'update'])->name('update');
    Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
});

// ==============================================
// Employee Management Routes
// ==============================================
    

Route::middleware(['auth', 'active.employee'])->prefix('employees')->name('employees.')->group(function () {
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
// Role Management Routes
// ==============================================

Route::middleware(['auth', 'active.employee'])->prefix('roles')->name('roles.')->group(function () {
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