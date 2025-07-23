<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientApiController;
use App\Http\Controllers\Api\PosTerminalApiController;
use App\Http\Controllers\Api\TechnicianApiController;
use App\Http\Controllers\Api\AssetApiController;
use App\Http\Controllers\Api\AssetRequestApiController;
use App\Http\Controllers\Api\AssetApprovalApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\Api\ProfileApiController;

// Public (guest) endpoints
Route::post('login',    [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Secured by Sanctum
Route::middleware('auth:sanctum')->group(function(){


Route::post('login',    [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Secured
Route::middleware('auth:sanctum')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    
});

    

    // Dashboard summary
    Route::get('dashboard', [ReportApiController::class, 'dashboard']);

    // Clients
    Route::apiResource('clients', ClientApiController::class);

    // POS Terminals + import/status
    Route::apiResource('pos-terminals', PosTerminalApiController::class);
    Route::post('pos-terminals/import', [PosTerminalApiController::class, 'import']);
    Route::post('pos-terminals/{id}/status', [PosTerminalApiController::class, 'updateStatus']);

    // Technicians + availability
    Route::apiResource('technicians', TechnicianApiController::class);
    Route::patch('technicians/{id}/availability', [TechnicianApiController::class, 'updateAvailability']);

    // Assets
    Route::apiResource('assets', AssetApiController::class);

    // Asset Requests (cart, checkout, my requests)
    Route::get('asset-requests/catalog', [AssetRequestApiController::class, 'catalog']);
    Route::post('asset-requests/cart/{asset}', [AssetRequestApiController::class, 'addToCart']);
    Route::get('asset-requests/cart', [AssetRequestApiController::class, 'viewCart']);
    Route::patch('asset-requests/cart/{asset}', [AssetRequestApiController::class, 'updateCart']);
    Route::delete('asset-requests/cart/{asset}', [AssetRequestApiController::class, 'removeFromCart']);
    Route::post('asset-requests/checkout', [AssetRequestApiController::class, 'checkout']);
    Route::apiResource('asset-requests', AssetRequestApiController::class)->only(['index','show','destroy']);

    // Asset Approvals
    Route::get('asset-approvals', [AssetApprovalApiController::class, 'index']);
    Route::post('asset-approvals/{id}/approve', [AssetApprovalApiController::class, 'approve']);
    Route::post('asset-approvals/{id}/reject',  [AssetApprovalApiController::class, 'reject']);

    // Tickets
    Route::apiResource('tickets', TicketApiController::class);

    // Reports builder & history
    Route::get('reports/summary', [ReportApiController::class, 'summary']);
    Route::post('reports/builder', [ReportApiController::class, 'build']);

    // Documents
    Route::get('documents', [DocumentApiController::class, 'index']);
    Route::post('documents', [DocumentApiController::class, 'upload']);

    // Profile
    Route::get('profile', [ProfileApiController::class, 'show']);
    Route::put('profile', [ProfileApiController::class, 'update']);
    Route::post('profile/password', [ProfileApiController::class, 'changePassword']);
});
