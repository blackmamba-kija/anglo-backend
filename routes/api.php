<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StationController;
use App\Http\Controllers\ConsumableItemController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryRequestController;
use App\Http\Controllers\ActivityLogController;

use App\Http\Controllers\LocalRecordController;

Route::post('login', [AuthController::class, 'login']);

Route::apiResource('stations', StationController::class);
Route::apiResource('consumables', ConsumableItemController::class);
Route::apiResource('assets', AssetController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('local-records', LocalRecordController::class);
Route::post('inventory-requests', [InventoryRequestController::class, 'store']);
Route::post('inventory-requests/{id}/accept', [InventoryRequestController::class, 'accept']);
Route::post('inventory-requests/{id}/reject', [InventoryRequestController::class, 'reject']);
Route::apiResource('inventory-requests', InventoryRequestController::class)->withoutMiddleware([\App\Http\Middleware\Authenticate::class]);
Route::apiResource('users', UserController::class);
Route::apiResource('alerts', AlertController::class);

Route::get('dashboard/stock-usage-trend', [DashboardController::class, 'stockUsageTrend']);

// Activity Logs
Route::get('activity-logs/stats', [ActivityLogController::class, 'stats']);
Route::delete('activity-logs/clear', [ActivityLogController::class, 'clear']);
Route::apiResource('activity-logs', ActivityLogController::class)->only(['index', 'store', 'destroy']);
