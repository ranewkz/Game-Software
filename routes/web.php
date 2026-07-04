<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// --- STOREFRONT ROUTES ---
Route::get('/', [GameController::class, 'index'])->name('storefront.index');

// --- AUTHENTICATION ROUTES ---
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout'); 

// --- CUSTOMER DASHBOARD ---
Route::get('/dashboard', [AuthController::class, 'customerDashboard'])->name('customer.dashboard');

// --- ADMIN DASHBOARD & CRUD ROUTES ---
Route::prefix('admin')->group(function () {
    
    // FIXED: Pointing this to the AuthController where we wrote all the new code!
    Route::get('/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
    
    Route::get('/games/create', [DashboardController::class, 'create'])->name('admin.games.create');
    Route::post('/games/add', [DashboardController::class, 'store'])->name('admin.games.store');
    Route::post('/games/update/{id}', [DashboardController::class, 'update'])->name('admin.games.update');
    Route::delete('/games/purge/{id}', [DashboardController::class, 'destroy'])->name('admin.games.destroy');
    Route::post('/profile/update', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');
    
    // --- THE BAN HAMMER ROUTE ---
    Route::post('/users/toggle-ban/{type}/{id}', [DashboardController::class, 'toggleBan'])->name('admin.users.ban');

    // --- THE ROLE OVERRIDE ROUTE ---
    Route::post('/users/update-role/{type}/{id}', [DashboardController::class, 'updateRole'])->name('admin.users.update_role');

    // --- NEW: DYNAMIC BANNER ROUTES ---
    Route::post('/banners/store', [AuthController::class, 'storeBanner'])->name('admin.banners.store');
    
    // FIXED: Removed the duplicate at the bottom and properly formatted this one
    Route::delete('/banners/destroy/{id}', [AuthController::class, 'destroyBanner'])->name('admin.banners.destroy');
});

// --- MISC ITEMS ---
Route::get('/items/create', [GameController::class, 'create'])->name('items.create');
Route::post('/items', [GameController::class, 'store'])->name('items.store');


Route::get('/profile', [App\Http\Controllers\AuthController::class, 'showProfile'])->name('profile');
Route::get('/my-orders', [App\Http\Controllers\AuthController::class, 'myOrders'])->name('my.orders');
Route::post('/profile/update', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update');