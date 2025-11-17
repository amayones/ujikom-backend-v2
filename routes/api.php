<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Admin\FilmController as AdminFilmController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\PriceController as AdminPriceController;
use App\Http\Controllers\Admin\SeatController as AdminSeatController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\Cashier\ScanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DiscountController;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
});

// Public Routes
Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{id}', [FilmController::class, 'show']);
Route::get('/schedules/{film_id}', [ScheduleController::class, 'getByFilm']);
Route::get('/seats/{schedule_id}', [SeatController::class, 'getBySchedule']);
Route::get('/studios', [StudioController::class, 'index']);
Route::get('/prices', [PriceController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::post('/checkout', [CustomerOrderController::class, 'checkout']);
    Route::post('/orders/{id}/cancel', [CustomerOrderController::class, 'cancel']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('films', AdminFilmController::class);
    Route::apiResource('users', AdminUserController::class);
    Route::post('users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus']);
    Route::post('users/{id}/reset-password', [AdminUserController::class, 'resetPassword']);
    Route::apiResource('schedules', AdminScheduleController::class);
    Route::apiResource('prices', AdminPriceController::class);
    Route::apiResource('seats', AdminSeatController::class);
    Route::apiResource('discounts', DiscountController::class);
});

// Discount Verification (Public for customers)
Route::post('/discounts/verify', [DiscountController::class, 'verify']);

// Owner Routes
Route::middleware(['auth:sanctum', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf']);
});

// Cashier Routes
Route::middleware(['auth:sanctum', 'role:cashier'])->prefix('cashier')->group(function () {
    Route::post('offline-order', [CashierOrderController::class, 'offlineOrder']);
    Route::get('orders', [ScanController::class, 'getAllPurchases']); // Alias untuk backward compatibility
    Route::get('my-orders', [ScanController::class, 'getMyOrders']);
    Route::get('all-purchases', [ScanController::class, 'getAllPurchases']);
    Route::post('/scan', [ScanController::class, 'scanAndUpdate']);
});

// Order Routes
Route::middleware(['auth:sanctum', 'role:customer,admin,owner'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});



// Payment Routes
Route::get('/payment/client-key', [PaymentController::class, 'getClientKey']);
Route::post('/payment/callback', [PaymentController::class, 'callback'])->withoutMiddleware(['auth:sanctum']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payment/snap-token/{orderId}', [PaymentController::class, 'getSnapToken']);
    Route::get('/payment/status/{orderNumber}', [PaymentController::class, 'checkStatus']);
});

// Utility Routes
Route::get('/debug-orders', [UtilityController::class, 'debugOrders']);
Route::get('/cleanup-pending-orders', [UtilityController::class, 'cleanupPendingOrders']);
Route::get('/fix-films-production', [UtilityController::class, 'fixFilmsProduction']);
