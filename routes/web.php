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

// --- CUSTOMER DASHBOARD & PROFILE ---
Route::get('/dashboard', [AuthController::class, 'customerDashboard'])->name('customer.dashboard');
Route::get('/catalog', [AuthController::class, 'catalog'])->name('customer.catalog');
Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
Route::get('/my-orders', [AuthController::class, 'myOrders'])->name('my.orders');

// --- SECURE CHECKOUT & WISHLIST ---
Route::get('/checkout', [AuthController::class, 'checkoutPage'])->name('checkout');
Route::post('/checkout', [AuthController::class, 'checkout'])->name('checkout.process');
Route::post('/wishlist/toggle', [AuthController::class, 'toggleWishlist'])->name('wishlist.toggle');
Route::get('/wishlist/get', [AuthController::class, 'getWishlist'])->name('wishlist.get');

// --- ADMIN DASHBOARD & CRUD ROUTES ---
Route::prefix('admin')->group(function () {
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

    // --- DYNAMIC BANNER ROUTES ---
    Route::post('/banners/store', [AuthController::class, 'storeBanner'])->name('admin.banners.store');
    Route::delete('/banners/destroy/{id}', [AuthController::class, 'destroyBanner'])->name('admin.banners.destroy');
});

// --- MISC ITEMS ---
Route::get('/items/create', [GameController::class, 'create'])->name('items.create');
Route::post('/items', [GameController::class, 'store'])->name('items.store');

// --- PROMO CODES ROUTES ---
Route::post('/admin/promos', [AuthController::class, 'storePromo'])->name('admin.promos.store');
Route::post('/checkout/check-promo', [AuthController::class, 'checkPromo'])->name('checkout.check-promo');