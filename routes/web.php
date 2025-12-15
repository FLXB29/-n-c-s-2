<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/seats',[App\Http\Controllers\EventController::class,'getSeats'])->name('events.seats');

Route::get('/api/events/{event}/seats', [SeatController::class, 'index'])->name('api.seats.index');
Route::middleware('auth')->group(function () {
    Route::post('/api/events/{event}/seats/hold', [SeatController::class, 'hold'])->name('api.seats.hold');
    Route::post('/api/events/{event}/seats/release', [SeatController::class, 'release'])->name('api.seats.release');
    Route::post('/api/events/{event}/seats/hold-multiple', [SeatController::class, 'holdMultiple'])->name('api.seats.holdMultiple');
    Route::post('/api/events/{event}/seats/release-all', [SeatController::class, 'releaseAll'])->name('api.seats.releaseAll');
});

Route::middleware('auth')->group(function () {
    Route::post('/events/{event}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/register/verify', [AuthController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [AuthController::class, 'verifyEmail']);
    
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    
    Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/avatar', [UserDashboardController::class, 'updateAvatar'])->name('avatar.update');
        Route::post('/change-password', [UserDashboardController::class, 'updatePassword'])->name('password.change');
        Route::post('/send-otp', [UserDashboardController::class, 'sendOtp'])->name('sendOtp');
        Route::post('/request-organizer', [UserDashboardController::class, 'requestOrganizer'])->name('requestOrganizer');
    });
    
    Route::middleware('role:organizer')->prefix('organizer')->name('organizer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'organizer'])->name('dashboard');
        Route::resource('events', EventController::class)->except(['index', 'show']);
        
        Route::get('/events/{event}/setup', [EventController::class, 'setup'])->name('events.setup');
        Route::post('/events/{event}/setup', [EventController::class, 'storeSetup'])->name('events.storeSetup');
    });
    
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{id}/role', [AdminController::class, 'toggleRole'])->name('toggleRole');
        Route::patch('/users/{id}/status', [AdminController::class, 'toggleStatus'])->name('toggleStatus');
        
        Route::get('/events', [AdminController::class, 'events'])->name('events.index');
        Route::patch('/events/{id}/approve', [AdminController::class, 'approveEvent'])->name('events.approve');
        Route::patch('/events/{id}/reject', [AdminController::class, 'rejectEvent'])->name('events.reject');
        Route::patch('/events/{id}/suspend', [AdminController::class, 'suspendEvent'])->name('events.suspend');
        Route::patch('/events/{id}/restore', [AdminController::class, 'restoreEvent'])->name('events.restore');
        
        Route::get('/requests', [AdminController::class, 'organizerRequests'])->name('requests.index');
        Route::patch('/requests/{id}/approve', [AdminController::class, 'approveOrganizer'])->name('requests.approve');
        Route::patch('/requests/{id}/reject', [AdminController::class, 'rejectOrganizer'])->name('requests.reject');

        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::patch('/orders/{id}/approve', [AdminController::class, 'approveOrder'])->name('orders.approve');
        Route::patch('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');
    });
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/events/{event}/checkout', [CheckoutController::class, 'process'])->name('events.checkout');
        Route::get('/orders/{order}/payment', [CheckoutController::class, 'showPayment'])->name('orders.payment');
        Route::post('/orders/{order}/confirm', [CheckoutController::class, 'confirmPayment'])->name('orders.confirm');
    });
    
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/messages/{userId}', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread');
        Route::patch('/messages/{messageId}/read', [ChatController::class, 'markAsRead'])->name('markRead');
        Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
    });
});

Route::get('/debug-db', function() {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE orders');
    dd($columns);
});

// AI Chatbot Routes
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/', [ChatbotController::class, 'index'])->name('index');
    Route::post('/send', [ChatbotController::class, 'sendMessage'])->name('send');
    Route::get('/suggestions', [ChatbotController::class, 'getSuggestions'])->name('suggestions');
});