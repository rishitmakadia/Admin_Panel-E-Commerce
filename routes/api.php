<?php

use App\Http\Controllers\API\CheckOutController;
use App\Http\Controllers\API\ElectronicsController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\UsersPurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::post('register', [UsersController::class, 'register']);
    Route::post('login', [UsersController::class, 'login']);
    Route::post('sendotp', [UsersController::class, 'sendOTP']);
    Route::post('setpwd', [UsersController::class, 'setPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [UsersController::class, 'logout']);
        Route::post('profile', [UsersController::class, 'profile']);
    });
});
Route::middleware('auth:api')->group(function () {
    Route::post('electronic', [ElectronicsController::class, 'electronic']);
    Route::post('electronic/category', [ElectronicsController::class, 'electronicCategory']);
    Route::post('electronic/category/subcategory', [ElectronicsController::class, 'electronicSubCategory']);
    Route::post('user/purchase', [UsersPurchaseController::class, 'purchase']);
    Route::post('user/show/cart', [UsersPurchaseController::class, 'showCartItems']);
    Route::post('user/add/cart', [UsersPurchaseController::class, 'addCartItems']);
    Route::post('user/address', [UsersController::class, 'setAddress']);
    Route::post('user/show/address', [UsersController::class, 'showAddress']);
    Route::post('user/delete/address', [UsersController::class, 'deleteAddress']);
    Route::post('user/checkout', [CheckOutController::class, 'payment']);
    Route::post('user/verify-checkout', [CheckOutController::class, 'verifyPayment']);
});
