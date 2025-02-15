<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\OrdersController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\CategoriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//middle roleAdmin
// Route::middleware(['auth:api', 'admin'])->group(function () {
//     Route::resource('role', RoleController::class);
// });


Route::prefix('v1')->group(function () {
    Route::apiResource('category', CategoriesController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('role', RoleController::class)->middleware(['auth:api', 'admin']);
    Route::post('order', [OrdersController::class, 'storeupdate'])->middleware(['auth:api', 'verifiedAccount']);
    Route::get('order', [OrdersController::class, 'index'])->middleware(['auth:api', 'verifiedAccount']);

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('profile', [ProfileController::class, 'storeupdate'])->middleware('auth:api');
        Route::post('login', [AuthController::class, 'login']);
        Route::get('me', [AuthController::class, 'currentuser'])->middleware('auth:api');
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('account_verification', [AuthController::class, 'verifikasi'])->middleware('auth:api');
        Route::post('generate_otp_code', [AuthController::class, 'generateOtp'])->middleware('auth:api');
    });

    
});
