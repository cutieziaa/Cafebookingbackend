<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderAdminController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    
    // Dashboard - semua user yang login bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Booking Routes - semua user yang login bisa akses
    Route::resource('booking', BookingController::class);
    Route::post('/booking/{booking}/status', [BookingController::class, 'updateStatus'])
         ->name('booking.update-status');
    Route::get('/booking/available-meja', [BookingController::class, 'getAvailableMeja'])
         ->name('booking.available-meja');
    
    // ==================== MENU ROUTES ====================

    // Semua user (view only)
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/{menu}', [MenuController::class, 'show'])->name('menu.show');

    // Hanya ADMIN yang bisa CRUD menu
    Route::prefix('test')->group(function () {
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('/menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

    Route::middleware(['customer'])->group(function () {
        Route::get('/order', [OrderController::class, 'index'])->name('order.index');
        Route::post('/order', [OrderController::class, 'store'])->name('order.store');
        Route::get('/order/history', [OrderController::class, 'history'])->name('order.history');
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/orders', [OrderAdminController::class, 'index'])->name('admin.orders');
        Route::post('/admin/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('admin.orders.status');
    });
    
    // ==================== MEJA ROUTES ====================
    // Hanya CS yang bisa mengelola meja
    Route::middleware(['cs'])->group(function () {
        Route::get('/meja', [MejaController::class, 'index'])->name('meja.index');
        Route::get('/meja/create', [MejaController::class, 'create'])->name('meja.create');
        Route::post('/meja', [MejaController::class, 'store'])->name('meja.store');
        Route::get('/meja/{meja}', [MejaController::class, 'show'])->name('meja.show');
        Route::get('/meja/{meja}/edit', [MejaController::class, 'edit'])->name('meja.edit');
        Route::put('/meja/{meja}', [MejaController::class, 'update'])->name('meja.update');
        Route::delete('/meja/{meja}', [MejaController::class, 'destroy'])->name('meja.destroy');
    });
    
    // ==================== USER ROUTES ====================
    // Hanya Admin yang bisa mengelola users
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit']);
    });
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});