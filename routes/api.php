<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Admin\FilmController as AdminFilmController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\PriceController as AdminPriceController;
use App\Http\Controllers\Admin\SeatController as AdminSeatController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Cashier\OrderController as CashierOrderController;
use App\Http\Controllers\PaymentController;

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
Route::get('/studios', function() {
    return response()->json([
        'success' => true,
        'message' => 'Studios retrieved successfully',
        'data' => \App\Models\Studio::all()
    ]);
});
Route::get('/check-films', function() {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Film::select('id', 'title', 'poster', 'trailer', 'created_at')->get()
    ]);
});
Route::get('/delete-old-films', function() {
    // Delete films that are not in the new seeder
    $newFilmTitles = [
        'Deadpool & Wolverine',
        'Inside Out 2',
        'Dune: Part Two',
        'Wicked',
        'Moana 2',
        'Gladiator II'
    ];
    
    $deleted = \App\Models\Film::whereNotIn('title', $newFilmTitles)->delete();
    
    return response()->json([
        'success' => true,
        'message' => "Deleted {$deleted} old films",
        'remaining_films' => \App\Models\Film::select('id', 'title')->get()
    ]);
});
Route::get('/prices', function() {
    return response()->json([
        'success' => true,
        'message' => 'Prices retrieved successfully',
        'data' => \App\Models\Price::all()
    ]);
});

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
});

// Owner Routes
Route::middleware(['auth:sanctum', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf']);
});

// Cashier Routes
Route::middleware(['auth:sanctum', 'role:cashier'])->prefix('cashier')->group(function () {
    Route::post('/offline-order', [CashierOrderController::class, 'offlineOrder']);
    Route::post('/online-order', [CashierOrderController::class, 'onlineOrder']);
    Route::get('/process-online/{order_id}', [CashierOrderController::class, 'processOnline']);
    Route::post('/print-ticket/{order_id}', [CashierOrderController::class, 'printTicket']);
    Route::post('/scan-ticket', [CashierOrderController::class, 'scanTicket']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', function() {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat'])->get()
        ]);
    });
    Route::get('/orders/{id}', function($id) {
        $order = \App\Models\Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat'])->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $order]);
    });
});

// Payment Routes
Route::get('/payment/client-key', [PaymentController::class, 'getClientKey']);
Route::post('/payment/callback', [PaymentController::class, 'callback'])->withoutMiddleware(['auth:sanctum']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payment/snap-token/{orderId}', [PaymentController::class, 'getSnapToken']);
    Route::get('/payment/status/{orderNumber}', [PaymentController::class, 'checkStatus']);
});

// Utility Routes (for production updates)
Route::get('/force-reseed', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        return response()->json([
            'success' => true,
            'message' => 'Database reseeded successfully',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
Route::get('/update-films', function() {
    $updates = [
        'Deadpool & Wolverine' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk'
        ],
        'Inside Out 2' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0'
        ],
        'Dune: Part Two' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w'
        ],
        'Wicked' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c'
        ],
        'Moana 2' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE'
        ],
        'Gladiator II' => [
            'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
            'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0'
        ]
    ];
    
    $results = [];
    foreach ($updates as $title => $data) {
        $film = \App\Models\Film::where('title', $title)->first();
        if ($film) {
            $film->update($data);
            $results[] = "Updated: {$title}";
        } else {
            $results[] = "Not found: {$title}";
        }
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Films updated',
        'results' => $results,
        'all_films' => \App\Models\Film::select('id', 'title', 'poster', 'trailer')->get()
    ]);
});
