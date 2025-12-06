<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SeatController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/seats', [App\Http\Controllers\EventController::class, 'getSeats'])->name('events.seats');

// Realtime Seats API (public để lấy danh sách, nhưng hold/release cần auth)
Route::get('/api/events/{event}/seats', [SeatController::class, 'index'])->name('api.seats.index');
Route::middleware('auth')->group(function () {
    Route::post('/api/events/{event}/seats/hold', [SeatController::class, 'hold'])->name('api.seats.hold');
    Route::post('/api/events/{event}/seats/release', [SeatController::class, 'release'])->name('api.seats.release');
    Route::post('/api/events/{event}/seats/hold-multiple', [SeatController::class, 'holdMultiple'])->name('api.seats.holdMultiple');
    Route::post('/api/events/{event}/seats/release-all', [SeatController::class, 'releaseAll'])->name('api.seats.releaseAll');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); //get là khi người dùng truy cập vào thì sẽ showform
    Route::post('/login', [AuthController::class, 'login']); //post là khi người dùng điền xong form đăng nhập thì sẽ gửi lên cho sv
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Social Login - Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Social Login - Facebook
    Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
});

//yêu cầu phải đăng nhập thì mới có thể vào được các route này đó chính là vai trò của middleware có sẵn auth
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User Dashboard Routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/avatar', [UserDashboardController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/change-password', [UserDashboardController::class, 'updatePassword'])->name('password.change');
        Route::post('/send-otp', [UserDashboardController::class, 'sendOtp'])->name('sendOtp');
        Route::post('/request-organizer', [UserDashboardController::class, 'requestOrganizer'])->name('requestOrganizer');
    });

    // Organizer Routes
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'organizer'])->name('dashboard');
        Route::resource('events', EventController::class)->except(['index', 'show']); //hỗ trợ crud

        // Setup Tickets & Seats
        Route::get('/events/{event}/setup', [EventController::class, 'setup'])->name('events.setup');
        Route::post('/events/{event}/setup', [EventController::class, 'storeSetup'])->name('events.storeSetup');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{id}/role', [AdminController::class, 'toggleRole'])->name('toggleRole');
        Route::patch('/users/{id}/status', [AdminController::class, 'toggleStatus'])->name('toggleStatus');

        // Event Management
        Route::get('/events', [AdminController::class, 'events'])->name('events.index');
        Route::patch('/events/{id}/approve', [AdminController::class, 'approveEvent'])->name('events.approve');
        Route::patch('/events/{id}/reject', [AdminController::class, 'rejectEvent'])->name('events.reject');
        Route::patch('/events/{id}/suspend', [AdminController::class, 'suspendEvent'])->name('events.suspend');
        Route::patch('/events/{id}/restore', [AdminController::class, 'restoreEvent'])->name('events.restore');

        // Organizer Requests
        Route::get('/requests', [AdminController::class, 'organizerRequests'])->name('requests.index');
        Route::patch('/requests/{id}/approve', [AdminController::class, 'approveOrganizer'])->name('requests.approve');
        Route::patch('/requests/{id}/reject', [AdminController::class, 'rejectOrganizer'])->name('requests.reject');

        // Order Management
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::patch('/orders/{id}/approve', [AdminController::class, 'approveOrder'])->name('orders.approve');
        Route::patch('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');
    });

    // Checkout Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/events/{event}/checkout', [CheckoutController::class, 'process'])->name('events.checkout');
        Route::get('/orders/{order}/payment', [CheckoutController::class, 'showPayment'])->name('orders.payment');
        Route::post('/orders/{order}/confirm', [CheckoutController::class, 'confirmPayment'])->name('orders.confirm');
    });
});

Route::get('/debug-db', function () {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE orders');
    dd($columns);
});
