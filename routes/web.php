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
use App\Http\Controllers\AdminStatisticsController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{event}/seats', [App\Http\Controllers\EventController::class, 'getSeats'])->name('events.seats');

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
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); //get là khi người dùng truy cập vào thì sẽ showform
    Route::post('/login', [AuthController::class, 'login']); //post là khi người dùng điền xong form đăng nhập thì sẽ gửi lên cho sv
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot password (OTP to email, then auto-login)
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');
    Route::get('/forgot-password/verify', [AuthController::class, 'showVerifyResetForm'])->name('password.verify');
    Route::post('/forgot-password/verify', [AuthController::class, 'verifyResetOtp'])->name('password.verify.submit');

    // === ROUTE MỚI CHO OTP ===
    Route::get('/register/verify', [AuthController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [AuthController::class, 'verifyEmail']);

    // Social Login - Google
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Social Login - Facebook
    Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
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

        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::patch('/orders/{id}/approve', [AdminController::class, 'approveOrder'])->name('orders.approve');
        Route::patch('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');

        // Statistics & Export
        Route::get('/statistics', [AdminStatisticsController::class, 'index'])->name('statistics');
        Route::get('/statistics/events', [AdminStatisticsController::class, 'eventStats'])->name('statistics.events');
        Route::get('/statistics/events/{event}', [AdminStatisticsController::class, 'eventDetail'])->name('statistics.event.detail');
        Route::get('/statistics/users', [AdminStatisticsController::class, 'userStats'])->name('statistics.users');
        Route::get('/statistics/users/{user}', [AdminStatisticsController::class, 'userDetail'])->name('statistics.user.detail');
        Route::get('/export/orders', [AdminStatisticsController::class, 'exportOrders'])->name('export.orders');
        Route::get('/export/users', [AdminStatisticsController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/events', [AdminStatisticsController::class, 'exportEvents'])->name('export.events');
        Route::get('/export/revenue', [AdminStatisticsController::class, 'exportRevenue'])->name('export.revenue');
        Route::get('/export/event/{event}', [AdminStatisticsController::class, 'exportEventDetail'])->name('export.event.detail');
        Route::get('/export/user/{user}', [AdminStatisticsController::class, 'exportUserDetail'])->name('export.user.detail');

        // Check-in QR Scanner
        Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in.index');
        Route::post('/check-in/scan', [CheckInController::class, 'scan'])->name('check-in.scan');
        Route::post('/check-in/confirm', [CheckInController::class, 'confirmCheckIn'])->name('check-in.confirm');
        Route::get('/check-in/event/{event}/stats', [CheckInController::class, 'getCheckedInList'])->name('check-in.stats');
    });

    // Checkout Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::post('/events/{event}/checkout', [CheckoutController::class, 'process'])->name('events.checkout');
        Route::get('/orders/{order}/payment', [CheckoutController::class, 'showPayment'])->name('orders.payment');
        Route::post('/orders/{order}/confirm', [CheckoutController::class, 'confirmPayment'])->name('orders.confirm');
    });

    // Chat Routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/messages/{userId}', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/unread-count', [ChatController::class, 'getUnreadCount'])->name('unread');
        Route::patch('/messages/{messageId}/read', [ChatController::class, 'markAsRead'])->name('markRead');
        Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
    });
});

Route::get('/debug-db', function () {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE orders');
    dd($columns);
});

// AI Chatbot Routes
Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/', [ChatbotController::class, 'index'])->name('index');
    Route::post('/send', [ChatbotController::class, 'sendMessage'])->name('send');
    Route::get('/suggestions', [ChatbotController::class, 'getSuggestions'])->name('suggestions');
});
