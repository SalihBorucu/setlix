<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('/', function () {
    return view('welcome');
});
