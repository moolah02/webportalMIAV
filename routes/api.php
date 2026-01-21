<?php

use App\Http\Controllers\Api\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobAssignmentController;
use App\Http\Controllers\Api\PosTerminalController;
use App\Http\Controllers\Api\AssetController as AssetApi;
use App\Http\Controllers\Api\AssetRequestController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\ReportsController;
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

Route::pattern('asset', '[0-9]+');
Route::pattern('assignment', '[0-9]+');

Route::middleware(['auth:sanctum'])->prefix('assets')->group(function () {
    // Static/collection endpoints FIRST
    Route::get('/',               [AssetApi::class, 'index']);
    Route::get('/categories',     [AssetApi::class, 'getCategories']);
    Route::get('/statistics',     [AssetApi::class, 'statistics']);
    Route::get('/requests',       [AssetApi::class, 'getRequests']);
    Route::post('/requests',      [AssetApi::class, 'createRequest']);
    Route::get('/my-assignments', [AssetApi::class, 'myAssignments']);
    Route::post('/assignments/{assignment}/return', [AssetApi::class, 'returnAsset'])
        ->whereNumber('assignment');

    // ID routes LAST (and constrained)
    Route::post('/{asset}/request', [AssetApi::class, 'requestAsset'])->whereNumber('asset');
    Route::get('/{asset}',          [AssetApi::class, 'show'])->whereNumber('asset');
});

    // ==============================================
    // ASSET REQUESTS API - COMPREHENSIVE
    // ==============================================

    Route::middleware(['auth:sanctum'])->prefix('asset-requests')->group(function () {
        // List all asset requests for authenticated user
        Route::get('/', [AssetRequestController::class, 'index']);

        // Get statistics
        Route::get('/stats', [AssetRequestController::class, 'stats']);

        // Get available assets for requesting
        Route::get('/available-assets', [AssetRequestController::class, 'availableAssets']);

        // Create new asset request
        Route::post('/', [AssetRequestController::class, 'store']);

        // Get specific request
        Route::get('/{id}', [AssetRequestController::class, 'show']);

        // Cancel a request
        Route::post('/{id}/cancel', [AssetRequestController::class, 'cancel']);
    });

    // ==============================================
    // REPORTS ROUTES - ENHANCED
    // ==============================================

    Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('tickets')->group(function () {
        // No pagination – return all (scoped to logged-in user)
        Route::get('/', [TicketController::class, 'index']);

        // Create a ticket
        Route::post('/', [TicketController::class, 'store']);

        // Specific ticket
        Route::get('/{ticket}', [TicketController::class, 'show']);
        Route::put('/{ticket}', [TicketController::class, 'update']);
        Route::delete('/{ticket}', [TicketController::class, 'destroy']);

        // Actions that exist in web routes
        Route::patch('/{ticket}/status', [TicketController::class, 'updateStatus']);

        // Additional API-specific routes
        Route::post('/{ticket}/comments', [TicketController::class, 'addComment']);
        Route::get('/{ticket}/history', [TicketController::class, 'getHistory']);

        // Optional: explicit “mine” shortcut
        Route::get('/mine/list', [TicketController::class, 'myTickets']);
    });
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

        // Export capabilities
        Route::get('/export/{type}', [ReportsController::class, 'export']);
        Route::post('/custom-report', [ReportsController::class, 'generateCustomReport']);

        // Report builder
        Route::get('/builder', [ReportsController::class, 'getBuilderData']);
        Route::post('/builder/generate', [ReportsController::class, 'generateBuilderReport']);
    });

    // ==============================================
    // SYNC & OFFLINE SUPPORT
    // ==============================================

    Route::prefix('sync')->group(function () {
        Route::get('/assignments', [JobAssignmentController::class, 'syncAssignments']);
        Route::get('/terminals', [PosTerminalController::class, 'syncTerminals']);
        Route::get('/clients', [ClientController::class, 'syncClients']);
    Route::get('/assets', [AssetApi::class, 'syncAssets']);
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
                'assets' => app(AssetApi::class)->syncAssets($request),
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
        $request->validate(['file' => 'required|file|51200']); // 10MB max

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
