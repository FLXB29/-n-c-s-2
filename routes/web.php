<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/seats',[App\Http\Controllers\EventController::class,'getSeats'])->name('events.seats');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

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
        Route::resource('events', EventController::class)->except(['index', 'show']);
        
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
    });
    
    // Checkout Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/events/{event}/checkout', [CheckoutController::class, 'process'])->name('events.checkout');
        Route::get('/orders/{order}/payment', [CheckoutController::class, 'showPayment'])->name('orders.payment');
        Route::post('/orders/{order}/confirm', [CheckoutController::class, 'confirmPayment'])->name('orders.confirm');
    });
});

Route::get('/debug-db', function() {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE orders');
    dd($columns);
});