<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controller yang akan kamu buat
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MejaTipeController;
use App\Http\Controllers\UserController;

// === PUBLIC ROUTES (Tidak perlu login) === //
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// === PROTECTED ROUTES (Harus login pakai token Sanctum) === //
Route::middleware('auth:sanctum')->group(function () {

    // === AUTH === //
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // === MENU (customer bisa lihat, admin full CRUD) === //
    Route::prefix('menu')->group(function () {
        Route::get('/', [MenuController::class, 'index']);
        Route::get('/{id}', [MenuController::class, 'show']);
        
        // Upload gambar (untuk semua user yang login)
        Route::post('/upload', [MenuController::class, 'upload']);
        
        // Admin only routes
        Route::middleware('admin')->group(function () {
            Route::post('/', [MenuController::class, 'store']);
            Route::put('/{id}', [MenuController::class, 'update']);
            Route::delete('/{id}', [MenuController::class, 'destroy']);
            Route::delete('/{id}/image', [MenuController::class, 'deleteImage']);
        });
    });


    Route::middleware('admin')->group(function () {
        Route::get('/meja-tipe', [MejaTipeController::class, 'index']);
        Route::post('/meja-tipe', [MejaTipeController::class, 'store']);
    });


    // === MEJA (admin manage, customer lihat) === //
    Route::get('/meja', [MejaController::class, 'index']);
    Route::get('/meja/available', [MejaController::class, 'getAvailable']);

    Route::middleware('admin')->group(function () {
        Route::post('/meja', [MejaController::class, 'store']);
        Route::put('/meja/{id}', [MejaController::class, 'update']);
        Route::delete('/meja/{id}', [MejaController::class, 'destroy']);
    });

    Route::prefix('meja-tipe')->group(function () {
        Route::get('/', [MejaTipeController::class, 'index']);
        Route::post('/', [MejaTipeController::class, 'store']);
    });


    // === BOOKING MEJA === //
// Booking routes
Route::prefix('bookings')->group(function () {
    Route::get('/get', [BookingController::class, 'index']); // Admin: all bookings
    Route::get('/my-bookings', [BookingController::class, 'myBookings']); // User: own bookings
    Route::post('/', [BookingController::class, 'store']); // Create booking
    Route::get('/available-tables', [BookingController::class, 'availableTables']); // Check available tables
    Route::get('/statistics', [BookingController::class, 'statistics']); // Statistics
    Route::get('/{id}', [BookingController::class, 'show']); // Booking detail
    Route::put('/{id}/status', [BookingController::class, 'updateStatus']); // Admin: update status
    Route::put('/{id}/cancel', [BookingController::class, 'cancel']); // User: cancel booking
});


    // === PICKUP (Takeaway order) === //
    Route::post('/pickup', [PickupController::class, 'store']); // Sudah support voucher
    Route::get('/pickup/me', [PickupController::class, 'myPickup']);
    // routes/api.php
    Route::post('/upload-payment', [OrderController::class, 'uploadPaymentProof']);

    Route::middleware('admin')->group(function () {
        Route::put('/pickup/{id}/status', [PickupController::class, 'updateStatus']);
        Route::get('/pickups', [PickupController::class, 'index']); // Untuk admin lihat semua
    });


    // === ORDER (POS: dine-in atau pickup) === //
    Route::post('/order', [OrderController::class, 'store']);  // buat order
    Route::get('/order/me', [OrderController::class, 'myOrders']);

    // admin proses order
    Route::middleware('admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::put('/orders/{id}/pay', [OrderController::class, 'markAsPaid']);
        Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    });


    // === VOUCHER === //
Route::prefix('vouchers')->group(function () {
    Route::get('/', [VoucherController::class, 'index']);          // GET /api/vouchers
    Route::post('/', [VoucherController::class, 'store']);         // POST /api/vouchers
    Route::get('/statistics', [VoucherController::class, 'statistics']); // GET /api/vouchers/statistics
    Route::get('/{id}', [VoucherController::class, 'show']);       // GET /api/vouchers/{id}
    Route::put('/{id}', [VoucherController::class, 'update']);     // PUT /api/vouchers/{id}
    Route::delete('/{id}', [VoucherController::class, 'destroy']); // DELETE /api/vouchers/{id}
    Route::get('/generate-code', [VoucherController::class, 'generateCode']); // GET /api/vouchers/generate-code
    Route::get('/check/{kode}', [VoucherController::class, 'check']); // GET /api/vouchers/check/{kode}
});

// User search (untuk voucher terbatas)
Route::get('/users/search', [UserController::class, 'search']);

});