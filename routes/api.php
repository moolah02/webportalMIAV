<?php
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobAssignmentController;
use App\Http\Controllers\Api\TerminalApiController;
use App\Http\Controllers\DeploymentPlanningController;
use Illuminate\Http\Request;
use App\Models\PosTerminal;

Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-device', [AuthController::class, 'registerDevice']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

    // Protected mobile API routes
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [MobileApiController::class, 'getDashboard']);
        
        // Sites and Terminals - ENHANCED
        Route::get('/sites/assigned', [MobileApiController::class, 'getAssignedSites']);
        Route::get('/sites/search', [MobileApiController::class, 'searchSites']);
        Route::get('/terminals/{terminal}', [TerminalApiController::class, 'getTerminal']);
        Route::patch('/terminals/{terminal}/status', [TerminalApiController::class, 'updateStatus']);
        Route::post('/terminals/{terminal}/visit', [TerminalApiController::class, 'recordVisit']);
        
        // Job Assignments
        Route::get('/assignments', [JobAssignmentController::class, 'getMyAssignments']);
        Route::get('/assignments/{assignment}', [JobAssignmentController::class, 'getAssignmentDetails']);
        Route::patch('/assignments/{assignment}/status', [JobAssignmentController::class, 'updateStatus']);
        Route::get('/regions/{regionId}/terminals', [JobAssignmentController::class, 'getRegionTerminals']);
        
        // Visit Management - ENHANCED
        Route::post('/visits', [MobileApiController::class, 'submitVisit']);
        Route::get('/visits/history', [MobileApiController::class, 'getVisitHistory']);
        Route::get('/visits/{visit}', [MobileApiController::class, 'getVisitDetails']);
        Route::patch('/visits/{visit}', [MobileApiController::class, 'updateVisit']);
        Route::delete('/visits/{visit}', [MobileApiController::class, 'deleteVisit']);
        
        // NEW: Bulk Terminal Updates (for when technicians visit multiple terminals)
        Route::post('/visits/bulk', [MobileApiController::class, 'submitBulkVisits']);
        Route::post('/terminals/bulk-update', [TerminalApiController::class, 'bulkUpdateTerminals']);
        
        // Ticket Management - ENHANCED
        Route::post('/tickets', [MobileApiController::class, 'createTicket']);
        Route::get('/tickets', [MobileApiController::class, 'getTickets']);
        Route::get('/tickets/{ticket}', [MobileApiController::class, 'getTicketDetails']);
        Route::patch('/tickets/{ticket}/status', [MobileApiController::class, 'updateTicketStatus']);
        Route::post('/tickets/{ticket}/comments', [MobileApiController::class, 'addTicketComment']);
        
        // Asset Requests
        Route::post('/asset-requests', [MobileApiController::class, 'submitAssetRequest']);
        Route::get('/asset-requests', [MobileApiController::class, 'getAssetRequests']);
        Route::get('/asset-requests/{request}', [MobileApiController::class, 'getAssetRequestDetails']);
        
        // Analytics
        Route::get('/analytics', [MobileApiController::class, 'getAnalytics']);
        Route::get('/analytics/performance', [MobileApiController::class, 'getPerformanceMetrics']);
        
        // Profile
        Route::get('/profile', [MobileApiController::class, 'getProfile']);
        Route::put('/profile', [MobileApiController::class, 'updateProfile']);
        
        // NEW: File Upload Support (for photos, signatures, documents)
        Route::post('/upload/photo', [MobileApiController::class, 'uploadPhoto']);
        Route::post('/upload/signature', [MobileApiController::class, 'uploadSignature']);
        Route::post('/upload/document', [MobileApiController::class, 'uploadDocument']);
        
        // Sync and Offline Support - ENHANCED
        Route::post('/sync/visits', [MobileApiController::class, 'syncVisits']);
        Route::post('/sync/tickets', [MobileApiController::class, 'syncTickets']);
        Route::post('/sync/terminals', [MobileApiController::class, 'syncTerminals']);
        Route::get('/sync/data', [MobileApiController::class, 'getSyncData']);
        Route::get('/sync/status', [MobileApiController::class, 'getSyncStatus']);
        Route::post('/sync/force', [MobileApiController::class, 'forceSyncData']);
        
        // NEW: Offline Data Management
        Route::get('/offline/assignments', [MobileApiController::class, 'getOfflineAssignments']);
        Route::get('/offline/terminals', [MobileApiController::class, 'getOfflineTerminals']);
        Route::post('/offline/queue', [MobileApiController::class, 'queueOfflineAction']);
        
        // NEW: Real-time Features
        Route::get('/notifications', [MobileApiController::class, 'getNotifications']);
        Route::patch('/notifications/{notification}/read', [MobileApiController::class, 'markNotificationRead']);
        Route::post('/location/update', [MobileApiController::class, 'updateLocation']);
        
        // NEW: Reporting and Export
        Route::get('/reports/daily', [MobileApiController::class, 'getDailyReport']);
        Route::get('/reports/weekly', [MobileApiController::class, 'getWeeklyReport']);
        Route::post('/reports/export', [MobileApiController::class, 'exportReport']);
        
        // NEW: Configuration and Settings
        Route::get('/config/app', [MobileApiController::class, 'getAppConfig']);
        Route::get('/config/categories', [MobileApiController::class, 'getCategories']);
        Route::get('/config/regions', [MobileApiController::class, 'getRegions']);
    });

    // Public routes (no authentication required)
    Route::get('/app/version', [MobileApiController::class, 'getAppVersion']);
    Route::get('/app/config/public', [MobileApiController::class, 'getPublicConfig']);
    
    // Emergency routes (for urgent situations)
    Route::post('/emergency/alert', [MobileApiController::class, 'sendEmergencyAlert']);
});

// Additional API endpoints for web dashboard integration
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/mobile-activity', [MobileApiController::class, 'getMobileActivity']);
    Route::get('/sync-logs', [MobileApiController::class, 'getSyncLogs']);
    Route::get('/device-registrations', [MobileApiController::class, 'getDeviceRegistrations']);
    Route::patch('/devices/{device}/status', [MobileApiController::class, 'updateDeviceStatus']);
});

Route::get('deployment/regions/{region}/terminals', 
    [c::class,'getRegionTerminals']
);

Route::get('deployment/options', function(Request $req){
    $col = match($req->group_by) {
        'region'  => 'region_id',
        'city'    => 'city',
        default   => 'physical_address',
    };

    $list = PosTerminal::where('client_id', $req->client)
        ->whereNotNull($col)
        ->distinct()
        ->pluck($col)
        ->map(fn($v) => ['id'=>$v, 'label'=>ucfirst($v)])
        ->values();

    return response()->json(['options'=>$list]);
});

Route::prefix('api')->name('api.')->middleware(['auth'])->group(function () {
    // ... your existing API routes ...
    
    // ADD these for JavaScript compatibility:
    Route::get('/clients/{client}/cities', [DeploymentPlanningController::class, 'getCitiesByClient']);
    Route::get('/clients/{client}/addresses', [DeploymentPlanningController::class, 'getAddressesByClient']);
    Route::get('/terminals', [DeploymentPlanningController::class, 'getFilteredTerminals']);
});
Route::get('/api/clients/{id}/cities', 'TerminalController@getCitiesByClient');
Route::get('/api/clients/{id}/addresses', 'TerminalController@getAddressesByClient');
Route::get('/api/terminals', 'TerminalController@getFilteredTerminals');