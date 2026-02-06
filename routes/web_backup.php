<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\PatientInfoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientInfoController as AdminPatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ResetPasswordController;

// EMERGENCY SIGNATURE ROUTES - PLACE AT TOP
Route::get('student/signature/{patient}', [StudentDashboardController::class, 'signature'])
    ->name('student.signature');

Route::get('patients/{patient}/signature', [PatientInfoController::class, 'signature'])
    ->middleware('admin.session')
    ->name('patients.signature');

// Test route to verify routes are working
Route::get('test-routes', function () {
    return response()->json([
        'message' => 'Routes file loaded successfully',
        'timestamp' => now(),
        'routes_registered' => true
    ]);
})->name('test.routes');

// Root redirect
Route::get('/', function () {
    return redirect()->route('index');
});

// Public pages
Route::get('/index', function () {
    return view('index');
})->name('index');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Student Login Routes
Route::get('student/login', function () {
    return view('student.login');
})->name('student.login');

Route::post('student/login', [StudentDashboardController::class, 'login'])
    ->name('student.login.submit');

Route::post('student/logout', [StudentDashboardController::class, 'logout'])
    ->name('student.logout');

// Student password reset routes  
Route::get('student/password/email', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('student.password.email');
Route::post('student/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('student.password.email.submit');

// Student password reset routes
Route::get('student/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('student.password.reset');
Route::post('student/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('student.password.reset.submit');

// Student Dashboard Routes (protected by middleware)
Route::middleware('student.session')->group(function () {
    Route::get('student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');
    Route::get('student/profile', [StudentDashboardController::class, 'profile'])
        ->name('student.profile');
    Route::put('student/password', [StudentDashboardController::class, 'updatePassword'])
        ->name('student.password.update');
    Route::get('student/consultations', [StudentDashboardController::class, 'consultations'])
        ->name('student.consultations');
    Route::get('student/uploads', [StudentDashboardController::class, 'uploads'])
        ->name('student.uploads');
});

// Admin routes with controller methods for consistency
Route::get('admin/dashboard', [DashboardController::class, 'index'])
    ->middleware('admin.session')
    ->name('admin.dashboard');

// Admin Login Routes
Route::get('admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('admin/login', [DashboardController::class, 'login'])
    ->name('admin.login.submit');

Route::post('admin/logout', [DashboardController::class, 'logout'])
    ->name('admin.logout');

// Admin dashboard data routes
Route::middleware('admin.session')->group(function () {
    Route::get('admin/dashboard/consultations-series', [DashboardController::class, 'consultationsSeries'])
        ->name('admin.dashboard.consultationsSeries');
    Route::get('admin/dashboard/inventory-series', [DashboardController::class, 'inventorySeries'])
        ->name('admin.dashboard.inventorySeries');

    // PDF download route
    Route::get('admin/consultations/pdf', [DashboardController::class, 'downloadConsultationsPdf'])
        ->name('admin.consultations.pdf');
});

// Patient routes
Route::middleware('admin.session')->group(function () {
    Route::resource('patients', AdminPatientController::class);
    Route::get('patients/create', [AdminPatientController::class, 'create'])->name('patients.create');
    Route::post('patients', [AdminPatientController::class, 'store'])->name('patients.store');
    Route::get('patients/{patient}/consultations', [AdminPatientController::class, 'consultations'])
        ->name('patients.consultations');
    Route::get('patients/{patient}/health-form', [AdminPatientController::class, 'healthForm'])
        ->name('patients.health-form');
    Route::post('patients/{patient}/health-form', [AdminPatientController::class, 'storeHealthForm'])
        ->name('patients.health-form.store');

    // Patient PDF download
    Route::get('patients/{patient}/pdf', [AdminPatientController::class, 'downloadPdf'])
        ->name('patients.pdf');
});

// Consultation routes
Route::middleware('admin.session')->group(function () {
    Route::resource('consultations', ConsultationController::class);
    Route::get('consultations/create', [ConsultationController::class, 'create'])
        ->name('consultations.create');
    Route::post('consultations', [ConsultationController::class, 'store'])
        ->name('consultations.store');
});

// Inventory routes 
Route::middleware('admin.session')->group(function () {
    Route::resource('inventory', InventoryController::class);
    Route::get('inventory/create', [InventoryController::class, 'create'])
        ->name('inventory.create');
    Route::post('inventory', [InventoryController::class, 'store'])
        ->name('inventory.store');
});
