<?php

use App\Http\Controllers\Api\Auth\AuthenticateController;
use App\Http\Controllers\Api\Auth\ClientController;
use App\Http\Controllers\Api\VerifyCode;
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

Route::post('/authenticate', [AuthenticateController::class, 'authenticate'])->name('authenticate');
Route::post('client', [ClientController::class, 'client'])->name('client');

Route::middleware(['auth:api'])->group(function () {
    Route::post('verify', [VerifyCode::class, 'verify'])->name('verify');

});