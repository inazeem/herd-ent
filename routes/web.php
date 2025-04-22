<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\BillingCodeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\MedicalFileController;
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

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->middleware('guest')->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->middleware('guest')->name('register');

Route::get('/dashboard', function () {
    return redirect()->route('reports.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes - only accessible by super-admin, admin, and administrator roles
    Route::middleware(['role:super-admin|administrator|admin'])->group(function () {
        // User management routes
        Route::resource('users', UserController::class);
        
        // Role management routes
        Route::resource('roles', RoleController::class);
    });

    
    // Invoice routes - accessible by users with invoice permissions
    Route::middleware(['permission:view invoices'])->group(function () {
        Route::resource('invoices', InvoiceController::class);
    });
    
    // Patient routes - accessible by users with patient permissions
    Route::middleware(['permission:view patients'])->group(function () {
        Route::resource('patients', \App\Http\Controllers\PatientController::class);
    });
    
    // Appointment routes - accessible by users with appointment permissions
    Route::middleware(['permission:view appointments'])->group(function () {
        Route::resource('appointments', \App\Http\Controllers\AppointmentController::class);
    });
    
    // Encounter routes - accessible by users with encounter permissions
    Route::middleware(['permission:view encounters'])->group(function () {
        Route::resource('encounters', \App\Http\Controllers\EncounterController::class);
    });
    
    // Medical File routes - accessible by users with appropriate permissions
    Route::middleware(['permission:view encounters'])->group(function () {
        Route::resource('medical-files', \App\Http\Controllers\MedicalFileController::class);
        Route::get('files/{file}/download', [\App\Http\Controllers\MedicalFileController::class, 'download'])->name('files.download');
    });
    
    // Billing code routes - accessible by users with billing code management permission
    Route::middleware(['permission:manage billing codes'])->group(function () {
        Route::resource('billing-codes', \App\Http\Controllers\BillingCodeController::class);
    });
    
    // Reports routes - accessible by users with reports permissions
    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/dashboard', [\App\Http\Controllers\ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports/financial', [\App\Http\Controllers\ReportController::class, 'financialReport'])->name('reports.financial');
        Route::get('/reports/clinical', [\App\Http\Controllers\ReportController::class, 'clinicalReport'])->name('reports.clinical');
        Route::get('/reports/patients', [\App\Http\Controllers\ReportController::class, 'patientReport'])->name('reports.patients');
        Route::get('/reports/appointments', [\App\Http\Controllers\ReportController::class, 'appointmentReport'])->name('reports.appointments');
        Route::get('/reports/users', [\App\Http\Controllers\ReportController::class, 'userReport'])->name('reports.users');
        
        // Export report routes
        Route::middleware(['permission:export reports'])->group(function () {
            Route::get('reports/{report}/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
        });
    });
});

require __DIR__.'/auth.php';
