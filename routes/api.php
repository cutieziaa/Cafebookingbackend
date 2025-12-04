<?php

use App\Http\Controllers\MejaTipeController;
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

// === PUBLIC ROUTES (Tidak perlu login) === //
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// === PROTECTED ROUTES (Harus login pakai token Sanctum) === //
Route::middleware('auth:sanctum')->group(function () {

    // === AUTH === //
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // === MENU (customer bisa lihat, admin full CRUD) === //
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/{id}', [MenuController::class, 'show']);

    // admin only
    Route::middleware('admin')->group(function () {
        Route::post('/menu', [MenuController::class, 'store']);
        Route::put('/menu/{id}', [MenuController::class, 'update']);
        Route::delete('/menu/{id}', [MenuController::class, 'destroy']);
    });


    Route::middleware('admin')->group(function () {
        Route::get('/meja-tipe', [MejaTipeController::class, 'index']);
        Route::post('/meja-tipe', [MejaTipeController::class, 'store']);
    });


    // === MEJA (admin manage, customer lihat) === //
    Route::get('/meja', [MejaController::class, 'index']);

    Route::middleware('admin')->group(function () {
        Route::post('/meja', [MejaController::class, 'store']);
        Route::put('/meja/{id}', [MejaController::class, 'update']);
        Route::delete('/meja/{id}', [MejaController::class, 'destroy']);
    });


    // === BOOKING MEJA === //
    Route::post('/booking', [BookingController::class, 'store']);
    Route::get('/booking/me', [BookingController::class, 'myBooking']);

    // admin konfirmasi
    Route::middleware('admin')->group(function () {
        Route::get('/booking', [BookingController::class, 'index']);
        Route::put('/booking/{id}/confirm', [AdminController::class, 'confirmBooking']);
        Route::put('/booking/{id}/cancel', [AdminController::class, 'cancelBooking']);
    });


    // === PICKUP (Takeaway order) === //
    Route::post('/pickup', [PickupController::class, 'store']);
    Route::get('/pickup/me', [PickupController::class, 'myPickup']);

    Route::middleware('admin')->group(function () {
        Route::put('/pickup/{id}/status', [PickupController::class, 'updateStatus']);
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
    Route::get('/voucher/check/{kode}', [VoucherController::class, 'check']);

    Route::middleware('admin')->group(function () {
        Route::post('/voucher', [VoucherController::class, 'store']);
        Route::put('/voucher/{id}', [VoucherController::class, 'update']);
        Route::delete('/voucher/{id}', [VoucherController::class, 'destroy']);
        Route::get('/voucher', [VoucherController::class, 'index']);
    });

});
