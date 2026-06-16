<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;

Route::get('/', [GameController::class, 'index'])->name('storefront.index');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/dashboard', [AuthController::class, 'customerDashboard'])->name('customer.dashboard');

Route::get('/items/create', [GameController::class, 'create'])->name('items.create');
Route::post('/items', [GameController::class, 'store'])->name('items.store');

Route::get('/admin-dashboard', function () {
    return view('admin_view.dashboard');
});

use App\Http\Controllers\DashboardController;
Route::get('/admin-dashboard', [DashboardController::class, 'index']);