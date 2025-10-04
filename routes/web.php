<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\pages\Electronics\ElectronicsCategoryController;
use App\Http\Controllers\pages\Electronics\ElectronicsController;
use App\Http\Controllers\pages\Electronics\ElectronicsSubCategoryController;
use App\Http\Controllers\pages\UsersAddressController;
use App\Http\Controllers\pages\UsersController;
use App\Http\Controllers\User\PageController;
use App\Http\Controllers\User\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('admin.profile');
        Route::get('electronics/categoryForm', [ElectronicsController::class, 'getCategoryForm']);
        Route::get('/data/all-categories', [ElectronicsCategoryController::class, 'index'])->name('data.categories.all');
        Route::get('/data/all-subcategories', [ElectronicsSubCategoryController::class, 'index'])->name('data.subcategories.all');
        Route::get('get-categories-by-electronic/{electronic}', [ElectronicsController::class, 'getCategories']);
        Route::get('electronics/subCategoryForm', [ElectronicsController::class, 'getSubCategoryForm']);

        Route::resource('users', UsersController::class);
        Route::resource('address', UsersAddressController::class);
        Route::resource('electronics', ElectronicsController::class);
        Route::resource('electronics.categories', ElectronicsCategoryController::class)->shallow();
        Route::resource('electronics.categories.subcategories', ElectronicsSubCategoryController::class)->shallow();
    });
});

Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
Route::post('/login', [UserAuthController::class, 'login']);
Route::get('/forgot-password', [UserAuthController::class, 'showForgotPassword'])->name('user.forgot');
Route::post('/forgot-password', [UserAuthController::class, 'forgotPassword']);
Route::get('/sendMail', [UserAuthController::class, 'sendMail'])->name('user.sendMail');
Route::get('/register', [UserAuthController::class, 'showRegisterForm'])->name('user.register');
Route::post('/register', [UserAuthController::class, 'register']);
Route::middleware('auth:user')->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
    Route::get('/home', [PageController::class, 'index'])->name('user.home');
    Route::get('/profile', [PageController::class, 'profile'])->name('user.profile');
    Route::get('/electronics', [PageController::class, 'electronics'])->name('user.electronics');
    Route::get('/checkout', [PageController::class, 'checkout'])->name('user.checkout');
});
