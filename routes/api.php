<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SepayWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Sepay banking webhook
Route::post('/sepay/webhook', [SepayWebhookController::class, 'handle'])->name('sepay.webhook');

// Order status polling (used on payment page)
Route::get('/orders/{order}/status', [SepayWebhookController::class, 'checkStatus'])->name('orders.status');
