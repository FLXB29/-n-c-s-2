<?php
// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');
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
    
    // User Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard.user');
    Route::post('/user/profile', [UserDashboardController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('/user/avatar', [UserDashboardController::class, 'updateAvatar'])->name('user.updateAvatar');
    
    // Password & OTP
    Route::post('/dashboard/send-otp', [UserDashboardController::class, 'sendOtp'])->name('dashboard.send-otp');
    Route::post('/dashboard/update-password', [UserDashboardController::class, 'updatePassword'])->name('dashboard.update-password');
    
    // Fix lỗi 404 nếu user truy cập /user/password -> Chuyển hướng về dashboard
    Route::get('/user/password', function () {
        return redirect('/dashboard#password');
    });
    
    // Organizer Routes
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'organizer'])->name('dashboard');
        Route::resource('events', EventController::class)->except(['index', 'show']);
    });
    
    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::patch('/users/{id}/role', [App\Http\Controllers\AdminController::class, 'toggleRole'])->name('toggleRole');
        Route::patch('/users/{id}/status', [App\Http\Controllers\AdminController::class, 'toggleStatus'])->name('toggleStatus');
    });
});