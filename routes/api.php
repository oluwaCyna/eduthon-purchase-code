<?php

use App\Http\Controllers\Auth\AddClientController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\VerifyPurchaseCodeController;
use App\Http\Controllers\SubscriptionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/authenticate', [AuthenticationController::class, 'authenticate'])->name('authenticate');
Route::post('client', [AddClientController::class, 'client'])->name('client');

Route::middleware(['auth:api'])->group(function () {
    Route::post('verify', [VerifyPurchaseCodeController::class, 'verify'])->name('verify');

    Route::post('/subscribe/flutter', [SubscriptionsController::class, 'handleFlutterwaveRequest'])->name('flutterwave');
    Route::patch('/activate', [SubscriptionsController::class, 'activate'])->name('activate');
});
