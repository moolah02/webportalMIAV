<?php

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


// Add this RIGHT after the "use" statements, before any other routes
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
    Route::post('/register', [AuthController::class, 'register']); // if needed
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// ==============================================
// PROTECTED API ROUTES
// ==============================================

Route::middleware(['auth:sanctum'])->group(function () {
    
    // ==============================================
    // USER & DASHBOARD
    // ==============================================
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // ==============================================
    // JOB ASSIGNMENTS (Core for technician mobile app)
    // ==============================================
    
    Route::prefix('jobs')->group(function () {
        // Get assignments for current user
        Route::get('/', [JobAssignmentController::class, 'index']);
        Route::get('/my-assignments', [JobAssignmentController::class, 'myAssignments']);
        Route::get('/{assignment}', [JobAssignmentController::class, 'show']);
        
        // Update assignment status
        Route::patch('/{assignment}/status', [JobAssignmentController::class, 'updateStatus']);
        Route::post('/{assignment}/start', [JobAssignmentController::class, 'startJob']);
        Route::post('/{assignment}/complete', [JobAssignmentController::class, 'completeJob']);
        Route::post('/{assignment}/pause', [JobAssignmentController::class, 'pauseJob']);
        
        // Add notes/photos to assignments
        Route::post('/{assignment}/notes', [JobAssignmentController::class, 'addNote']);
        Route::post('/{assignment}/photos', [JobAssignmentController::class, 'uploadPhoto']);
        Route::get('/{assignment}/photos', [JobAssignmentController::class, 'getPhotos']);
        
        // Location tracking
        Route::post('/{assignment}/location', [JobAssignmentController::class, 'updateLocation']);
        Route::post('/{assignment}/checkin', [JobAssignmentController::class, 'checkIn']);
        Route::post('/{assignment}/checkout', [JobAssignmentController::class, 'checkOut']);
    });
    
    // ==============================================
    // POS TERMINALS (Enhanced with Chart Data)
    // ==============================================
    
    Route::prefix('pos-terminals')->group(function () {
        Route::get('/', [PosTerminalController::class, 'index']);
        Route::get('/{terminal}', [PosTerminalController::class, 'show']);
        Route::patch('/{terminal}/status', [PosTerminalController::class, 'updateStatus']);
        Route::post('/{terminal}/service-report', [PosTerminalController::class, 'createServiceReport']);
        Route::get('/{terminal}/history', [PosTerminalController::class, 'getHistory']);
        
        // NEW: Chart Data & Enhanced Statistics
        Route::get('/chart-data', [PosTerminalController::class, 'getChartData']);
        Route::get('/statistics/comprehensive', [PosTerminalController::class, 'getComprehensiveStats']);
        Route::get('/statistics/service-timeline', [PosTerminalController::class, 'getServiceTimelineData']);
        Route::get('/statistics/distributions', [PosTerminalController::class, 'getDistributionData']);
        
        // Search and filters
        Route::get('/search/{query}', [PosTerminalController::class, 'search']);
        Route::get('/by-region/{region}', [PosTerminalController::class, 'getByRegion']);
        Route::get('/by-client/{client}', [PosTerminalController::class, 'getByClient']);
        
        // NEW: Enhanced filtering with statistics
        Route::post('/filtered-stats', [PosTerminalController::class, 'getFilteredStatistics']);
    });

    // ==============================================
    // REPORTS & ANALYTICS (Enhanced)
    // ==============================================
    
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard', [ReportsController::class, 'dashboardStats']);
        Route::get('/assignments', [ReportsController::class, 'assignmentReports']);
        Route::get('/terminals', [ReportsController::class, 'terminalReports']);
        Route::get('/technician-performance', [ReportsController::class, 'technicianPerformance']);
        Route::get('/my-performance', [ReportsController::class, 'myPerformance']);
        
        // NEW: Enhanced Analytics Endpoints
        Route::get('/terminal-analytics', [ReportsController::class, 'getTerminalAnalytics']);
        Route::get('/service-analytics', [ReportsController::class, 'getServiceAnalytics']);
        Route::get('/performance-metrics', [ReportsController::class, 'getPerformanceMetrics']);
        Route::get('/trends-analysis', [ReportsController::class, 'getTrendsAnalysis']);
    });
    
    // ==============================================
    // CLIENTS
    // ==============================================
    
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{client}', [ClientController::class, 'show']);
        Route::get('/{client}/terminals', [ClientController::class, 'getTerminals']);
        Route::get('/{client}/projects', [ClientController::class, 'getProjects']);
    });
    
    // ==============================================
    // ASSETS
    // ==============================================
    
    Route::prefix('assets')->group(function () {
        Route::get('/', [AssetController::class, 'index']);
        Route::get('/{asset}', [AssetController::class, 'show']);
        Route::post('/{asset}/request', [AssetController::class, 'requestAsset']);
        Route::get('/my-assignments', [AssetController::class, 'myAssignments']);
        Route::post('/assignments/{assignment}/return', [AssetController::class, 'returnAsset']);
        
        // Asset requests
        Route::get('/requests', [AssetController::class, 'getRequests']);
        Route::post('/requests', [AssetController::class, 'createRequest']);
        Route::patch('/requests/{request}/status', [AssetController::class, 'updateRequestStatus']);
    });
    
    // ==============================================
    // TICKETS
    // ==============================================
    
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{ticket}', [TicketController::class, 'show']);
        Route::patch('/{ticket}/status', [TicketController::class, 'updateStatus']);
        Route::post('/{ticket}/comments', [TicketController::class, 'addComment']);
        Route::get('/my-tickets', [TicketController::class, 'myTickets']);
    });
    
    // ==============================================
    // TECHNICIANS (for managers)
    // ==============================================
    
    Route::prefix('technicians')->group(function () {
        Route::get('/', [TechnicianController::class, 'index']);
        Route::get('/{technician}', [TechnicianController::class, 'show']);
        Route::get('/{technician}/assignments', [TechnicianController::class, 'getAssignments']);
        Route::get('/{technician}/location', [TechnicianController::class, 'getCurrentLocation']);
        Route::patch('/{technician}/availability', [TechnicianController::class, 'updateAvailability']);
    });
    
   
    
    // ==============================================
    // SYNC & OFFLINE SUPPORT
    // ==============================================
    
    Route::prefix('sync')->group(function () {
        Route::get('/assignments', [JobAssignmentController::class, 'syncAssignments']);
        Route::get('/terminals', [PosTerminalController::class, 'syncTerminals']);
        Route::post('/bulk-update', [JobAssignmentController::class, 'bulkUpdate']);
        Route::get('/timestamp', function() {
            return response()->json(['timestamp' => now()->toISOString()]);
        });
    });
    
    // ==============================================
    // NOTIFICATIONS
    // ==============================================
    
    Route::prefix('notifications')->group(function () {
        Route::get('/', function(Request $request) {
            return $request->user()->notifications()->paginate();
        });
        Route::post('/{notification}/read', function($notificationId, Request $request) {
            $request->user()->notifications()->where('id', $notificationId)->markAsRead();
            return response()->json(['success' => true]);
        });
        Route::post('/mark-all-read', function(Request $request) {
            $request->user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
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
    
});

// ==============================================
// PUBLIC API ROUTES (no authentication required)
// ==============================================

Route::prefix('public')->group(function () {
    Route::get('/app-version', function() {
        return response()->json([
            'version' => '1.0.0',
            'min_version' => '1.0.0',
            'update_required' => false
        ]);
    });
    
    Route::get('/maintenance', function() {
        return response()->json([
            'maintenance_mode' => false,
            'message' => null
        ]);
    });
});