<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuoteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes - only accessible by super-admin and admin roles
    Route::middleware(['role:super-admin|admin'])->group(function () {
        // User management routes
        Route::resource('users', UserController::class);
        
        // Role management routes
        Route::resource('roles', RoleController::class);
    });

    // Management routes - accessible by managers and above
    Route::middleware(['role:super-admin|admin|manager'])->group(function () {
        // Invoice management routes
        Route::resource('invoices', InvoiceController::class);
        
        // Quote management routes
        Route::resource('quotes', QuoteController::class);
    });
});

require __DIR__.'/auth.php';
