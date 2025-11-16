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

// Cleanup old pending orders
Route::get('/cleanup-pending-orders', function() {
    try {
        // Delete pending orders older than 1 hour
        $deleted = \App\Models\Order::where('payment_status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old pending orders"
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

// Fix production films - single endpoint
Route::get('/fix-films-production', function() {
    try {
        // Delete old films
        $oldFilms = ['Oppenheimer', 'The Batman', 'Spider-Man', 'Top Gun', 'Avengers'];
        foreach ($oldFilms as $title) {
            \App\Models\Film::where('title', 'LIKE', "%{$title}%")->delete();
        }
        
        // Ensure new films exist with correct data
        $films = [
            ['title' => 'Deadpool & Wolverine', 'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg', 'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk', 'genre' => 'Action, Comedy', 'duration' => 128, 'status' => 'play_now', 'description' => 'Deadpool teams up with Wolverine in an epic adventure across the multiverse.', 'base_price' => 55000],
            ['title' => 'Inside Out 2', 'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg', 'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0', 'genre' => 'Animation, Family', 'duration' => 96, 'status' => 'play_now', 'description' => 'Riley enters puberty and experiences a whole new set of emotions.', 'base_price' => 45000],
            ['title' => 'Dune: Part Two', 'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg', 'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w', 'genre' => 'Sci-Fi, Adventure', 'duration' => 166, 'status' => 'play_now', 'description' => 'Paul Atreides unites with Chani and the Fremen to seek revenge.', 'base_price' => 52000],
            ['title' => 'Wicked', 'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg', 'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c', 'genre' => 'Fantasy, Musical', 'duration' => 160, 'status' => 'coming_soon', 'description' => 'The untold story of the Witches of Oz before Dorothy arrived.', 'base_price' => 50000],
            ['title' => 'Moana 2', 'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg', 'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE', 'genre' => 'Animation, Adventure', 'duration' => 100, 'status' => 'coming_soon', 'description' => 'Moana embarks on a new oceanic adventure with her friends.', 'base_price' => 48000],
            ['title' => 'Gladiator II', 'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg', 'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0', 'genre' => 'Action, Drama', 'duration' => 148, 'status' => 'coming_soon', 'description' => 'The epic saga continues with a new gladiator rising to power.', 'base_price' => 55000]
        ];
        
        foreach ($films as $filmData) {
            \App\Models\Film::updateOrCreate(
                ['title' => $filmData['title']],
                $filmData
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Films fixed successfully',
            'films' => \App\Models\Film::select('id', 'title', 'poster')->get()
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});
