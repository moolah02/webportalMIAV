<?php

use App\Http\Controllers\Api\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobAssignmentController;
use App\Http\Controllers\Api\PosTerminalController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\BusinessLicenseController;
//use App\Http\Controllers\Api\AssetRequestController;
use App\Http\Controllers\Api\AssetApprovalController;
use App\Http\Controllers\Api\TerminalDeploymentController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\TechnicianReportsController;
use App\Http\Controllers\Api\VisitController as ApiVisitController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\ReportTemplateController;

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'message' => 'API route works!',
        'timestamp' => now(),
        'sanctum_installed' => class_exists('Laravel\Sanctum\PersonalAccessToken')
    ]);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ==============================================
// AUTHENTICATION ROUTES (PUBLIC)
// ==============================================

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::patch('/profile/password', [AuthController::class, 'updatePassword']);
    });
});

// ==============================================
// PROTECTED API ROUTES
// ==============================================

Route::middleware(['auth:sanctum'])->group(function () {

    // ==============================================
    // DASHBOARD ROUTES
    // ==============================================

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/technician', [DashboardController::class, 'index']);
        Route::get('/employee', [DashboardController::class, 'employee']);
        Route::get('/admin', [DashboardController::class, 'admin']);
        Route::get('/manager', [DashboardController::class, 'manager']);
    });

    // ==============================================
    // CLIENT MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']); // No pagination - return all
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{client}', [ClientController::class, 'show']);
        Route::put('/{client}', [ClientController::class, 'update']);
        Route::delete('/{client}', [ClientController::class, 'destroy']);
        Route::get('/{client}/terminals', [ClientController::class, 'getTerminals']);
        Route::get('/{client}/projects', [ClientController::class, 'getProjects']);
        Route::get('/{client}/statistics', [ClientController::class, 'getStatistics']);
    });

    // ==============================================
    // POS TERMINAL ROUTES - COMPREHENSIVE
    // ==============================================

    Route::prefix('pos-terminals')->group(function () {
        // Main CRUD routes
        Route::get('/', [PosTerminalController::class, 'index']); // No pagination - return all
        Route::post('/', [PosTerminalController::class, 'store']);
        Route::get('/{terminal}', [PosTerminalController::class, 'show']);
        Route::put('/{terminal}', [PosTerminalController::class, 'update']);
        Route::delete('/{terminal}', [PosTerminalController::class, 'destroy']);
        Route::patch('/{terminal}/status', [PosTerminalController::class, 'updateStatus']);

        Route::match(['put','patch'], '/id/{id}', [PosTerminalController::class, 'updateById']);

        // Update by terminal_id
    Route::match(['put','patch'], '/pos-terminals/{terminalId}', [PosTerminalController::class, 'updateByTerminalId']);

    // Bulk updates by terminal_id
    Route::patch('/pos-terminals/bulk', [PosTerminalController::class, 'bulkUpdateByTerminalId']);

        // Chart and Statistics
        Route::get('/chart-data', [PosTerminalController::class, 'getChartData']);
        Route::get('/statistics/comprehensive', [PosTerminalController::class, 'getComprehensiveStats']);
        Route::get('/statistics/service-timeline', [PosTerminalController::class, 'getServiceTimelineData']);
        Route::get('/statistics/distributions', [PosTerminalController::class, 'getDistributionData']);
        Route::post('/filtered-stats', [PosTerminalController::class, 'getFilteredStatistics']);

        // Import/Export
        Route::post('/import', [PosTerminalController::class, 'import']);
        Route::post('/preview-import', [PosTerminalController::class, 'previewImport']);
        Route::post('/preview-import-enhanced', [PosTerminalController::class, 'previewImportEnhanced']);
        Route::get('/export', [PosTerminalController::class, 'export']);
        Route::get('/download-template', [PosTerminalController::class, 'downloadTemplate']);

        // Column Mapping
        Route::get('/column-mapping', [PosTerminalController::class, 'getColumnMapping']);
        Route::post('/column-mapping', [PosTerminalController::class, 'storeColumnMapping']);
        Route::delete('/column-mapping/{mapping}', [PosTerminalController::class, 'deleteColumnMapping']);
        Route::patch('/column-mapping/{mapping}/toggle', [PosTerminalController::class, 'toggleColumnMapping']);

        // Terminal-specific actions
        Route::post('/{terminal}/service-report', [PosTerminalController::class, 'createServiceReport']);
        Route::get('/{terminal}/history', [PosTerminalController::class, 'getHistory']);
        Route::post('/{terminal}/tickets', [PosTerminalController::class, 'createTicket']);
        Route::post('/{terminal}/services', [PosTerminalController::class, 'scheduleService']);
        Route::post('/{terminal}/notes', [PosTerminalController::class, 'addNote']);
        Route::get('/{terminal}/reports/{type}', [PosTerminalController::class, 'generateReport']);
        Route::get('/{terminal}/statistics', [PosTerminalController::class, 'getStatistics']);

        // Search and filters
        Route::get('/search/{query}', [PosTerminalController::class, 'search']);
        Route::get('/by-region/{region}', [PosTerminalController::class, 'getByRegion']);
        Route::get('/by-client/{client}', [PosTerminalController::class, 'getByClient']);
    });


    // ==============================================
    // SITE VISITS ROUTES
    // ==============================================

    // Top-level visits API (filterable index, create, show)

Route::middleware('auth:sanctum')->prefix('visits')->group(function () {
    Route::get('/all', [VisitController::class, 'index']);     // ?assignmentId=&employeeId=&merchantId=&dateFrom=&dateTo=
    Route::post('/', [VisitController::class, 'store']);    // POST your payload
    Route::get('/{visit}', [VisitController::class, 'show']);
});

// Convenience: visits nested under jobs/assignments (same auth)
Route::middleware('auth:sanctum')->prefix('jobs')->group(function () {
    Route::get('/{assignment}/visits',   [VisitController::class, 'indexByAssignment']);
    Route::post('/{assignment}/visits',  [VisitController::class, 'storeForAssignment']); // assignmentId auto-filled
});

    // ==============================================
    // JOB ASSIGNMENT ROUTES - MATCHING WEB PATTERNS
    // ==============================================

    Route::prefix('jobs')->group(function () {
        // List routes - matching web controller methods
        Route::get('/', [JobAssignmentController::class, 'index']); // All assignments for managers
        Route::get('/assignments', [JobAssignmentController::class, 'index']); // Alternative endpoint
        Route::get('/mine', [JobAssignmentController::class, 'mine']); // My assignments for technicians
        Route::get('/my-assignments', [JobAssignmentController::class, 'mine']); // Alternative endpoint

        // CRUD operations (from jobs.assignment routes in web)
        Route::post('/', [JobAssignmentController::class, 'store']);
        Route::get('/{assignment}', [JobAssignmentController::class, 'show']);
        Route::put('/{assignment}', [JobAssignmentController::class, 'update']);
        Route::delete('/{assignment}', [JobAssignmentController::class, 'destroy']);

        // Status management - matching web routes
        Route::patch('/{assignment}/status', [JobAssignmentController::class, 'updateStatus']);
        Route::post('/{assignment}/cancel', [JobAssignmentController::class, 'cancel']);

        // Additional mobile/API specific routes (ensure these methods exist)
        Route::post('/{assignment}/start', [JobAssignmentController::class, 'startJob']);
        Route::post('/{assignment}/complete', [JobAssignmentController::class, 'completeJob']);
        Route::post('/{assignment}/pause', [JobAssignmentController::class, 'pauseJob']);

        // Notes and photos (ensure these methods exist in API controller)
        Route::post('/{assignment}/notes', [JobAssignmentController::class, 'addNote']);
        Route::post('/{assignment}/photos', [JobAssignmentController::class, 'uploadPhoto']);
        Route::get('/{assignment}/photos', [JobAssignmentController::class, 'getPhotos']);

        // Location tracking (API-specific)
        Route::post('/{assignment}/location', [JobAssignmentController::class, 'updateLocation']);
        Route::post('/{assignment}/checkin', [JobAssignmentController::class, 'checkIn']);
        Route::post('/{assignment}/checkout', [JobAssignmentController::class, 'checkOut']);

        // Utility routes from web
        Route::get('/regions/{region}/terminals', [JobAssignmentController::class, 'getRegionTerminals']);
        Route::get('/export', [JobAssignmentController::class, 'export']);
    });

    // ==============================================
    // TECHNICIAN MANAGEMENT ROUTES
    // ==============================================

    Route::prefix('technicians')->group(function () {
        Route::get('/', [TechnicianController::class, 'index']); // No pagination - return all
        Route::post('/', [TechnicianController::class, 'store']);
        Route::get('/{technician}', [TechnicianController::class, 'show']);
        Route::put('/{technician}', [TechnicianController::class, 'update']);
        Route::delete('/{technician}', [TechnicianController::class, 'destroy']);
        Route::patch('/{technician}/availability', [TechnicianController::class, 'updateAvailability']);
        Route::get('/{technician}/assignments', [TechnicianController::class, 'getAssignments']);
        Route::get('/{technician}/location', [TechnicianController::class, 'getCurrentLocation']);
        Route::get('/{technician}/performance', [TechnicianController::class, 'getPerformance']);
        Route::get('/{technician}/schedule', [TechnicianController::class, 'getSchedule']);
    });

    // ==============================================
    // ASSET MANAGEMENT ROUTES - COMPREHENSIVE
    // ==============================================

    Route::prefix('assets')->group(function () {
        // Main CRUD routes
        Route::get('/', [AssetController::class, 'index']); // No pagination - return all
        Route::post('/', [AssetController::class, 'store']);
        Route::get('/{asset}', [AssetController::class, 'show']);
        Route::put('/{asset}', [AssetController::class, 'update']);
        Route::delete('/{asset}', [AssetController::class, 'destroy']);

        // Stock management
        Route::post('/{asset}/update-stock', [AssetController::class, 'updateStock']);
        Route::post('/bulk-update-stock', [AssetController::class, 'bulkUpdateStock']);

        // Asset assignments
        Route::post('/assign', [AssetController::class, 'assignAsset']);
        Route::get('/my-assignments', [AssetController::class, 'myAssignments']);
        Route::post('/assignments/{assignment}/return', [AssetController::class, 'returnAsset']);
        Route::patch('/assignments/{assignment}/transfer', [AssetController::class, 'transferAsset']);
        Route::get('/assignments/{assignment}/data', [AssetController::class, 'getAssignmentData']);

        // Reports and exports
        Route::get('/export', [AssetController::class, 'export']);
        Route::get('/low-stock-alerts', [AssetController::class, 'lowStockAlerts']);
        Route::get('/{asset}/vehicle-info', [AssetController::class, 'getVehicleInfo']);
        Route::get('/assignment-report', [AssetController::class, 'assignmentReport']);
        Route::get('/overdue-report', [AssetController::class, 'overdueReport']);

        // Asset requests
        Route::get('/requests', [AssetController::class, 'getRequests']);
        Route::post('/requests', [AssetController::class, 'createRequest']);
        Route::post('/{asset}/request', [AssetController::class, 'requestAsset']);
        Route::patch('/requests/{request}/status', [AssetController::class, 'updateRequestStatus']);

        // Utility routes
        Route::get('/available-employees', [AssetController::class, 'getAvailableEmployees']);
        Route::get('/categories', [AssetController::class, 'getCategories']);
        Route::get('/statistics', [AssetController::class, 'getStatistics']);
    });
/*
    // ==============================================
    // ASSET REQUEST ROUTES - COMPREHENSIVE
    // ==============================================

    Route::prefix('asset-requests')->group(function () {
        Route::get('/', [AssetRequestController::class, 'index']); // No pagination - return all
        Route::post('/', [AssetRequestController::class, 'store']);
        Route::get('/{request}', [AssetRequestController::class, 'show']);
        Route::patch('/{request}/cancel', [AssetRequestController::class, 'cancel']);

        // Catalog and cart functionality
        Route::get('/catalog', [AssetRequestController::class, 'catalog']);
        Route::post('/cart/add/{asset}', [AssetRequestController::class, 'addToCart']);
        Route::get('/cart', [AssetRequestController::class, 'getCart']);
        Route::patch('/cart/{asset}', [AssetRequestController::class, 'updateCart']);
        Route::delete('/cart/{asset}', [AssetRequestController::class, 'removeFromCart']);
        Route::get('/checkout', [AssetRequestController::class, 'getCheckout']);
        Route::post('/checkout', [AssetRequestController::class, 'checkout']);

        // Status management
        Route::patch('/{request}/status', [AssetRequestController::class, 'updateStatus']);
        Route::get('/by-status/{status}', [AssetRequestController::class, 'getByStatus']);
        Route::get('/statistics', [AssetRequestController::class, 'getStatistics']);
    });

    // ==============================================
    // ASSET APPROVAL ROUTES - NEW
    // ==============================================

    Route::prefix('asset-approvals')->group(function () {
        Route::get('/', [AssetApprovalController::class, 'index']); // No pagination - return all
        Route::get('/{approval}', [AssetApprovalController::class, 'show']);
        Route::post('/{approval}/approve', [AssetApprovalController::class, 'approve']);
        Route::post('/{approval}/reject', [AssetApprovalController::class, 'reject']);
        Route::post('/bulk-action', [AssetApprovalController::class, 'bulkAction']);
        Route::get('/statistics', [AssetApprovalController::class, 'getStats']);
        Route::get('/export', [AssetApprovalController::class, 'exportReport']);
        Route::get('/pending', [AssetApprovalController::class, 'getPending']);
        Route::get('/history', [AssetApprovalController::class, 'getHistory']);
    });
*/
    // ==============================================
    // EMPLOYEE MANAGEMENT ROUTES - NEW
    // ==============================================

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']); // No pagination - return all
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{employee}', [EmployeeController::class, 'show']);
        Route::put('/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/{employee}', [EmployeeController::class, 'destroy']);

        // Quick actions
        Route::patch('/{employee}/role', [EmployeeController::class, 'updateRole']);
        Route::patch('/{employee}/status', [EmployeeController::class, 'toggleStatus']);
        Route::patch('/{employee}/activate', [EmployeeController::class, 'activate']);
        Route::patch('/{employee}/deactivate', [EmployeeController::class, 'deactivate']);

        // Employee-specific data
        Route::get('/{employee}/assignments', [EmployeeController::class, 'getAssignments']);
        Route::get('/{employee}/performance', [EmployeeController::class, 'getPerformance']);
        Route::get('/{employee}/assets', [EmployeeController::class, 'getAssets']);
        Route::get('/{employee}/tickets', [EmployeeController::class, 'getTickets']);

        // Utility routes
        Route::get('/by-role/{role}', [EmployeeController::class, 'getByRole']);
        Route::get('/by-department/{department}', [EmployeeController::class, 'getByDepartment']);
        Route::get('/active', [EmployeeController::class, 'getActive']);
        Route::get('/inactive', [EmployeeController::class, 'getInactive']);
        Route::get('/statistics', [EmployeeController::class, 'getStatistics']);
        Route::get('/export', [EmployeeController::class, 'export']);
    });

    // ==============================================
    // ROLE MANAGEMENT ROUTES - NEW
    // ==============================================

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']); // No pagination - return all
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::put('/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'destroy']);

        // Role management actions
        Route::post('/{role}/clone', [RoleController::class, 'clone']);
        Route::patch('/{role}/permissions', [RoleController::class, 'updatePermissions']);
        Route::get('/{role}/employees', [RoleController::class, 'getEmployees']);
        Route::get('/{role}/permissions', [RoleController::class, 'getPermissions']);

        // Utility routes
        Route::get('/permissions/available', [RoleController::class, 'getAvailablePermissions']);
        Route::get('/statistics', [RoleController::class, 'getStatistics']);
    });

    // ==============================================
    // BUSINESS LICENSE ROUTES - NEW
    // ==============================================

    Route::prefix('business-licenses')->group(function () {
        Route::get('/', [BusinessLicenseController::class, 'index']); // No pagination - return all
        Route::post('/', [BusinessLicenseController::class, 'store']);
        Route::get('/{license}', [BusinessLicenseController::class, 'show']);
        Route::put('/{license}', [BusinessLicenseController::class, 'update']);
        Route::delete('/{license}', [BusinessLicenseController::class, 'destroy']);

        // License-specific actions
        Route::post('/{license}/renew', [BusinessLicenseController::class, 'renew']);
        Route::post('/{license}/process-renewal', [BusinessLicenseController::class, 'processRenewal']);
        Route::get('/{license}/download', [BusinessLicenseController::class, 'downloadDocument']);

        // Reports and filters
        Route::get('/expiring', [BusinessLicenseController::class, 'getExpiring']);
        Route::get('/expired', [BusinessLicenseController::class, 'getExpired']);
        Route::get('/active', [BusinessLicenseController::class, 'getActive']);
        Route::get('/compliance-report', [BusinessLicenseController::class, 'getComplianceReport']);
        Route::get('/statistics', [BusinessLicenseController::class, 'getFilteredStats']);
        Route::get('/export', [BusinessLicenseController::class, 'export']);

        // Categories and types
        Route::get('/types', [BusinessLicenseController::class, 'getTypes']);
        Route::get('/by-type/{type}', [BusinessLicenseController::class, 'getByType']);
    });

    // ==============================================
    // TICKET MANAGEMENT ROUTES - MATCHING WEB
    // ==============================================

    Route::prefix('tickets')->group(function () {
        // Basic CRUD - matches web resource routes
        Route::get('/', [TicketController::class, 'index']); // No pagination - return all
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{ticket}', [TicketController::class, 'show']);
        Route::put('/{ticket}', [TicketController::class, 'update']);
        Route::delete('/{ticket}', [TicketController::class, 'destroy']);

        // Actions that exist in web routes
        Route::patch('/{ticket}/status', [TicketController::class, 'updateStatus']);
        Route::post('/{ticket}/assign', [TicketController::class, 'assignTicket']);

        // Additional API-specific routes (ensure these methods exist in controller)
        Route::post('/{ticket}/comments', [TicketController::class, 'addComment']);
        Route::get('/{ticket}/history', [TicketController::class, 'getHistory']);

        // User-specific tickets (ensure method exists)
        Route::get('/assigned-to-me', function(Request $request) {
            return app(\App\Http\Controllers\Api\TicketController::class)->index($request->merge(['assigned_to' => auth()->id()]));
        });
    });

    // ==============================================
    // DEPLOYMENT ROUTES - NEW COMPREHENSIVE
    // ==============================================

    Route::prefix('deployment')->group(function () {
        // Main deployment data
        Route::get('/', [TerminalDeploymentController::class, 'index']); // No pagination - return all
        Route::get('/initial-data', [TerminalDeploymentController::class, 'getInitialData']);
        Route::get('/hierarchical', [TerminalDeploymentController::class, 'getHierarchical']);

        // Project management
        Route::post('/projects', [TerminalDeploymentController::class, 'getProjectsByClients']);
        Route::post('/projects/create', [TerminalDeploymentController::class, 'createProject']);
        Route::patch('/projects/{project}', [TerminalDeploymentController::class, 'updateProject']);
        Route::get('/projects/{project}', [TerminalDeploymentController::class, 'getProject']);
        Route::delete('/projects/{project}', [TerminalDeploymentController::class, 'deleteProject']);

        // Terminal management
        Route::post('/terminals', [TerminalDeploymentController::class, 'getHierarchicalTerminals']);
        Route::get('/terminals/unassigned', [TerminalDeploymentController::class, 'getUnassignedTerminals']);
        Route::get('/terminals/assigned', [TerminalDeploymentController::class, 'getAssignedTerminals']);

        // Assignment operations
        Route::post('/assign', [TerminalDeploymentController::class, 'createAssignment']);
        Route::post('/bulk-assign', [TerminalDeploymentController::class, 'bulkAssign']);
        Route::post('/quick-assign', [TerminalDeploymentController::class, 'quickAssignTerminal']);
        Route::post('/auto-assign', [TerminalDeploymentController::class, 'autoAssignTerminals']);

        // Assignment management
        Route::get('/assignments/{assignment}', [TerminalDeploymentController::class, 'getAssignmentDetails']);
        Route::patch('/assignments/{assignment}', [TerminalDeploymentController::class, 'updateAssignment']);
        Route::delete('/assignments/{assignment}', [TerminalDeploymentController::class, 'cancelAssignment']);

        // Work orders and deployment
        Route::post('/work-orders', [TerminalDeploymentController::class, 'generateWorkOrders']);
        Route::post('/deploy', [TerminalDeploymentController::class, 'deployAll']);
        Route::get('/progress/{deployment}', [TerminalDeploymentController::class, 'getDeploymentProgress']);

        // Draft management
        Route::post('/drafts', [TerminalDeploymentController::class, 'saveAsDraft']);
        Route::get('/drafts', [TerminalDeploymentController::class, 'getDrafts']);
        Route::get('/drafts/{draft}', [TerminalDeploymentController::class, 'loadDraft']);
        Route::delete('/drafts/{draft}', [TerminalDeploymentController::class, 'deleteDraft']);

        // Statistics and reporting
        Route::get('/statistics', [TerminalDeploymentController::class, 'getStatistics']);
        Route::get('/technician-workload', [TerminalDeploymentController::class, 'getTechnicianWorkload']);
        Route::get('/regional-summary', [TerminalDeploymentController::class, 'getRegionalSummary']);
        Route::get('/export/{format}', [TerminalDeploymentController::class, 'exportAssignments']);

        // Mobile and sync
        Route::get('/mobile-sync', [TerminalDeploymentController::class, 'mobileSync']);
    });

    // ==============================================
    // REPORTS ROUTES - ENHANCED
    // ==============================================

    Route::prefix('reports')->group(function () {
        // Dashboard and overview
        Route::get('/', [ReportsController::class, 'index']);
        Route::get('/dashboard', [ReportsController::class, 'dashboardStats']);

        // Core reports
        Route::get('/assignments', [ReportsController::class, 'assignmentReports']);
        Route::get('/terminals', [ReportsController::class, 'terminalReports']);
        Route::get('/technician-performance', [ReportsController::class, 'technicianPerformance']);
        Route::get('/my-performance', [ReportsController::class, 'myPerformance']);

        // Enhanced Analytics
        Route::get('/terminal-analytics', [ReportsController::class, 'getTerminalAnalytics']);
        Route::get('/service-analytics', [ReportsController::class, 'getServiceAnalytics']);
        Route::get('/performance-metrics', [ReportsController::class, 'getPerformanceMetrics']);
        Route::get('/trends-analysis', [ReportsController::class, 'getTrendsAnalysis']);

        // Technician Visit Reports
        Route::get('/technician-visits', [TechnicianReportsController::class, 'index']);
        Route::get('/technician-visits/filter', [TechnicianReportsController::class, 'filter']);
        Route::get('/technician-visits/{visit}', [TechnicianReportsController::class, 'show']);
        Route::get('/technician-visits/{visit}/photos', [TechnicianReportsController::class, 'getPhotos']);
        Route::get('/technician-visits/{visit}/pdf', [TechnicianReportsController::class, 'generatePDF']);
        Route::get('/technician-visits/export', [TechnicianReportsController::class, 'export']);

        // Export capabilities
        Route::get('/export/{type}', [ReportsController::class, 'export']);
        Route::post('/custom-report', [ReportsController::class, 'generateCustomReport']);

        // Report builder
        Route::get('/builder', [ReportsController::class, 'getBuilderData']);
        Route::post('/builder/generate', [ReportsController::class, 'generateBuilderReport']);
    });

    // ==============================================
    // SETTINGS ROUTES - NEW
    // ==============================================

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::get('/all', [SettingsController::class, 'getAllSettings']);
        Route::post('/update', [SettingsController::class, 'updateSettings']);

        // Category Management
        Route::get('/categories/{type}', [SettingsController::class, 'getCategory']);
        Route::post('/categories/{type}', [SettingsController::class, 'storeCategory']);
        Route::put('/categories/{category}', [SettingsController::class, 'updateCategory']);
        Route::delete('/categories/{category}', [SettingsController::class, 'deleteCategory']);
        Route::post('/categories/reorder', [SettingsController::class, 'updateCategoryOrder']);

        // System settings
        Route::get('/system', [SettingsController::class, 'getSystemSettings']);
        Route::post('/system', [SettingsController::class, 'updateSystemSettings']);

        // User preferences
        Route::get('/preferences', [SettingsController::class, 'getUserPreferences']);
        Route::post('/preferences', [SettingsController::class, 'updateUserPreferences']);
    });

    // ==============================================
    // DOCUMENT MANAGEMENT ROUTES - NEW
    // ==============================================

    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']); // No pagination - return all
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::put('/{document}', [DocumentController::class, 'update']);
        Route::delete('/{document}', [DocumentController::class, 'destroy']);

        // Document actions
        Route::post('/upload', [DocumentController::class, 'upload']);
        Route::get('/{document}/download', [DocumentController::class, 'download']);
        Route::post('/{document}/share', [DocumentController::class, 'share']);
        Route::get('/{document}/versions', [DocumentController::class, 'getVersions']);

        // Categories and folders
        Route::get('/categories', [DocumentController::class, 'getCategories']);
        Route::get('/by-category/{category}', [DocumentController::class, 'getByCategory']);
        Route::get('/folders', [DocumentController::class, 'getFolders']);
        Route::post('/folders', [DocumentController::class, 'createFolder']);
        Route::get('/folders/{folder}', [DocumentController::class, 'getFolderContents']);

        // Search and filters
        Route::get('/search/{query}', [DocumentController::class, 'search']);
        Route::get('/recent', [DocumentController::class, 'getRecent']);
        Route::get('/my-documents', [DocumentController::class, 'getMyDocuments']);
        Route::get('/shared-with-me', [DocumentController::class, 'getSharedWithMe']);
    });

    // ==============================================
    // SYNC & OFFLINE SUPPORT
    // ==============================================

    Route::prefix('sync')->group(function () {
        Route::get('/assignments', [JobAssignmentController::class, 'syncAssignments']);
        Route::get('/terminals', [PosTerminalController::class, 'syncTerminals']);
        Route::get('/clients', [ClientController::class, 'syncClients']);
        Route::get('/assets', [AssetController::class, 'syncAssets']);
        Route::get('/employees', [EmployeeController::class, 'syncEmployees']);
        Route::get('/tickets', [TicketController::class, 'syncTickets']);

        Route::post('/bulk-update', [JobAssignmentController::class, 'bulkUpdate']);
        Route::post('/bulk-sync', function(Request $request) {
            return response()->json([
                'assignments' => app(JobAssignmentController::class)->syncAssignments($request),
                'terminals' => app(PosTerminalController::class)->syncTerminals($request),
                'timestamp' => now()->toISOString()
            ]);
        });

        Route::get('/timestamp', function() {
            return response()->json(['timestamp' => now()->toISOString()]);
        });

        Route::get('/full-sync', function(Request $request) {
            return response()->json([
                'assignments' => app(JobAssignmentController::class)->syncAssignments($request),
                'terminals' => app(PosTerminalController::class)->syncTerminals($request),
                'clients' => app(ClientController::class)->syncClients($request),
                'assets' => app(AssetController::class)->syncAssets($request),
                'timestamp' => now()->toISOString()
            ]);
        });
    });

    // ==============================================
    // NOTIFICATIONS
    // ==============================================

    Route::prefix('notifications')->group(function () {
        Route::get('/', function(Request $request) {
            return response()->json($request->user()->notifications);
        });
        Route::post('/{notification}/read', function($notificationId, Request $request) {
            $request->user()->notifications()->where('id', $notificationId)->markAsRead();
            return response()->json(['success' => true]);
        });
        Route::post('/mark-all-read', function(Request $request) {
            $request->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        });
        Route::get('/unread', function(Request $request) {
            return response()->json($request->user()->unreadNotifications);
        });
        Route::get('/count', function(Request $request) {
            return response()->json(['count' => $request->user()->unreadNotifications->count()]);
        });
    });

    // ==============================================
    // FILE UPLOADS
    // ==============================================

    Route::post('/upload/photo', function(Request $request) {
        $request->validate(['photo' => 'required|image|max:2048']);

        $path = $request->file('photo')->store('uploads/photos', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    });

    Route::post('/upload/document', function(Request $request) {
        $request->validate(['document' => 'required|file|max:5120']); // 5MB max

        $path = $request->file('document')->store('uploads/documents', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    });

    Route::post('/upload/avatar', function(Request $request) {
        $request->validate(['avatar' => 'required|image|max:1024']);

        $path = $request->file('avatar')->store('uploads/avatars', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    });

    Route::post('/upload/attachment', function(Request $request) {
        $request->validate(['file' => 'required|file|max:10240']); // 10MB max

        $path = $request->file('file')->store('uploads/attachments', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'name' => $request->file('file')->getClientOriginalName(),
            'size' => $request->file('file')->getSize()
        ]);
    });

    // ==============================================
    // UTILITY ROUTES
    // ==============================================

    Route::prefix('utils')->group(function () {
        Route::get('/search', function(Request $request) {
            $query = $request->get('q');
            $type = $request->get('type', 'all');

            $results = [];

            if ($type === 'all' || $type === 'terminals') {
                $terminals = app(PosTerminalController::class)->search($query);
                $results['terminals'] = $terminals;
            }

            if ($type === 'all' || $type === 'clients') {
                $clients = app(ClientController::class)->search($query);
                $results['clients'] = $clients;
            }

            if ($type === 'all' || $type === 'employees') {
                $employees = app(EmployeeController::class)->search($query);
                $results['employees'] = $employees;
            }

            return response()->json($results);
        });

        Route::get('/dashboard-stats', function(Request $request) {
            return response()->json([
                'terminals' => [
                    'total' => \App\Models\PosTerminal::count(),
                    'active' => \App\Models\PosTerminal::where('status', 'active')->count(),
                    'inactive' => \App\Models\PosTerminal::where('status', 'inactive')->count(),
                ],
                'assignments' => [
                    'total' => \App\Models\JobAssignment::count(),
                    'pending' => \App\Models\JobAssignment::where('status', 'pending')->count(),
                    'in_progress' => \App\Models\JobAssignment::where('status', 'in_progress')->count(),
                    'completed' => \App\Models\JobAssignment::where('status', 'completed')->count(),
                ],
                'tickets' => [
                    'total' => \App\Models\Ticket::count(),
                    'open' => \App\Models\Ticket::where('status', 'open')->count(),
                    'closed' => \App\Models\Ticket::where('status', 'closed')->count(),
                ],
            ]);
        });

        Route::get('/quick-stats', function() {
            return response()->json([
                'timestamp' => now()->toISOString(),
                'system_status' => 'operational',
                'maintenance_mode' => false,
            ]);
        });
    });

});

// ==============================================
// PUBLIC API ROUTES (no authentication required)
// ==============================================

Route::prefix('public')->group(function () {
    Route::get('/app-version', function() {
        return response()->json([
            'version' => '1.0.0',
            'min_version' => '1.0.0',
            'update_required' => false,
            'features' => [
                'offline_sync',
                'photo_upload',
                'location_tracking',
                'push_notifications'
            ]
        ]);
    });

    Route::get('/maintenance', function() {
        return response()->json([
            'maintenance_mode' => false,
            'message' => null,
            'scheduled_maintenance' => null
        ]);
    });

    Route::get('/status', function() {
        return response()->json([
            'status' => 'operational',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'services' => [
                'api' => 'operational',
                'database' => 'operational',
                'storage' => 'operational'
            ]
        ]);
    });
});

// ==============================================
// WEBHOOK ROUTES (for external integrations)
// ==============================================

Route::prefix('webhooks')->group(function () {
    Route::post('/terminal-update', function(Request $request) {
        // Handle terminal status updates from external systems
        return response()->json(['received' => true]);
    });

    Route::post('/job-completion', function(Request $request) {
        // Handle job completion webhooks
        return response()->json(['received' => true]);
    });
});

//mobile api for visit page//



Route::middleware(['auth:sanctum','throttle:60,1'])->group(function () {
    Route::get('/visits', [ApiVisitController::class, 'index']);
    Route::get('/visits/{visit}', [ApiVisitController::class, 'show']);
    Route::get('/assignments/{assignmentId}/visits', [ApiVisitController::class, 'indexByAssignment']);

    Route::post('/visits', [ApiVisitController::class, 'store']);
    Route::post('/assignments/{assignmentId}/visits', [ApiVisitController::class, 'storeForAssignment']);

    // Optional evidence upload
    Route::post('/visits/{visit}/evidence', [ApiVisitController::class, 'uploadEvidence']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
});

//reports routes


Route::middleware(['auth:sanctum'])->group(function () {
    // Report operations
    Route::prefix('report')->group(function () {
        Route::post('/preview', [ReportController::class, 'preview'])
            ->name('api.report.preview');
        Route::post('/export', [ReportController::class, 'export'])
            ->name('api.report.export');
        Route::get('/fields', [ReportController::class, 'getAvailableFields'])
            ->name('api.report.fields');
    });

    // Report template CRUD
    Route::prefix('report/templates')->group(function () {
        Route::get('/', [ReportTemplateController::class, 'index'])
            ->name('api.report.templates.index');
        Route::post('/', [ReportTemplateController::class, 'store'])
            ->name('api.report.templates.store');
        Route::get('/{id}', [ReportTemplateController::class, 'show'])
            ->name('api.report.templates.show');
        Route::put('/{id}', [ReportTemplateController::class, 'update'])
            ->name('api.report.templates.update');
        Route::delete('/{id}', [ReportTemplateController::class, 'destroy'])
            ->name('api.report.templates.destroy');
        Route::post('/{id}/duplicate', [ReportTemplateController::class, 'duplicate'])
            ->name('api.report.templates.duplicate');
        Route::get('/tags/list', [ReportTemplateController::class, 'getTags'])
            ->name('api.report.templates.tags');
    });
});
