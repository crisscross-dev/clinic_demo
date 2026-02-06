<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\UnifiedAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientInfoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PendingPatientController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\StockTransactionController;
use App\Http\Controllers\StudentAccountController;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

// Index/Landing page route
Route::get('/', [HomeController::class, 'index'])->name('home');


// Unified Login Route (handles both admin and student login)
Route::get('login', [UnifiedAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [UnifiedAuthController::class, 'login'])->name('unified.login');
Route::post('logout', [UnifiedAuthController::class, 'logout'])->name('logout');

// Legacy admin routes (use unified login)
Route::get('admin/login', [UnifiedAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [UnifiedAuthController::class, 'login']);

// Admin forgot password (AJAX) - returns JSON
Route::post('admin/password/email', [\App\Http\Controllers\Admin\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('admin.password.email');

// Admin password reset routes
Route::get('admin/password/reset/{token}', [\App\Http\Controllers\Admin\ResetPasswordController::class, 'showResetForm'])
    ->name('admin.password.reset');
Route::post('admin/password/reset', [\App\Http\Controllers\Admin\ResetPasswordController::class, 'reset'])
    ->name('admin.password.update');

// Alias route expected by Laravel's password notification
Route::get('password/reset/{token}', [\App\Http\Controllers\Admin\ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Student Authentication Routes
Route::get('student/login', [StudentAuthController::class, 'showLoginForm'])
    ->name('student.login');
Route::post('student/login', [StudentAuthController::class, 'login']);
Route::get('student/register', [StudentAuthController::class, 'showRegistrationForm'])
    ->name('student.register');
Route::post('student/register', [\App\Http\Controllers\RegistrationController::class, 'register']);
Route::get('verify-registration/{token}', [\App\Http\Controllers\RegistrationController::class, 'verify'])->name('verify.registration');
Route::post('student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Student forgot password (AJAX) - returns JSON
Route::post('student/password/email', [\App\Http\Controllers\Student\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('student.password.email');

// Student password reset routes
Route::get('student/password/reset/{token}', [\App\Http\Controllers\Admin\ResetPasswordController::class, 'showResetForm'])
    ->name('student.password.reset');
Route::post('student/password/reset', [\App\Http\Controllers\Admin\ResetPasswordController::class, 'reset'])
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
    // Student consultation PDF download (single consultation)
    Route::get('student/consultations/{consultation}/download', [StudentDashboardController::class, 'downloadConsultationPdf'])
        ->name('student.consultations.download');

    // Student profile PDF download
    Route::get('student/profile/pdf/{patient}', [StudentDashboardController::class, 'downloadProfilePdf'])
        ->name('student.profile.pdf');

    // Accept privacy policy
    Route::post('student/privacy-policy/accept', [StudentDashboardController::class, 'acceptPrivacyPolicy'])
        ->name('student.privacy.accept');
});

// Student signature route (outside middleware for testing)
Route::get('student/signature/{patient}', [StudentDashboardController::class, 'signature'])
    ->name('student.signature');


// Admin routes with controller methods for consistency
Route::get('admin/dashboard', [DashboardController::class, 'index'])
    ->middleware('admin.session')
    ->name('admin.dashboard');

// Dashboard data endpoints
Route::get('admin/dashboard/consultations-series', [DashboardController::class, 'consultationsSeries'])
    ->middleware('admin.session')
    ->name('admin.dashboard.consultationsSeries');

// Inventory series (used/restock) for selectable start/end
Route::get('admin/dashboard/inventory-series', [DashboardController::class, 'inventorySeries'])
    ->middleware('admin.session')
    ->name('admin.dashboard.inventorySeries');

// Admin consultations PDF download
Route::get('admin/consultations/pdf', [DashboardController::class, 'downloadConsultationsPdf'])
    ->middleware('admin.session')
    ->name('admin.consultations.pdf');

// Patient form submission route (public)
Route::post('patient/submit', [PatientInfoController::class, 'store'])->name('patient.submit');


// Patients
Route::resource('patients', PatientInfoController::class)->middleware('admin.session');

// Consent Management
Route::get('patients/consent/requests', [PatientInfoController::class, 'consentRequests'])
    ->middleware('admin.session')
    ->name('patients.consent.requests');

Route::post('patients/{patient}/toggle-consent', [PatientInfoController::class, 'toggleConsent'])
    ->middleware('admin.session')
    ->name('patients.toggleConsent');

Route::post('patients/bulk-toggle-consent', [PatientInfoController::class, 'bulkToggleConsent'])
    ->middleware('admin.session')
    ->name('patients.bulkToggleConsent');

Route::post('patients/schedule-consent-access', [PatientInfoController::class, 'scheduleConsentAccess'])
    ->middleware('admin.session')
    ->name('patients.scheduleConsentAccess');

// Student request consent access (no middleware - accessible to students)
Route::post('patient/request-consent-access', [PatientInfoController::class, 'requestConsentAccess'])
    ->name('patient.requestConsentAccess');

// Patient uploads page (UI) - renders the uploads blade which includes the partial
Route::get('patients/{patient}/uploads', function (App\Models\PatientInfo $patient) {
    return view('patients.uploads', compact('patient'));
})->middleware('admin.session')->name('patients.uploads');

// Patient uploads
Route::get('patient/uploads', [\App\Http\Controllers\PatientUploadController::class, 'index'])->name('patient.uploads.index');
Route::post('patient/uploads', [\App\Http\Controllers\PatientUploadController::class, 'store'])->name('patient.uploads.store');
Route::delete('patient/uploads/{upload}', [\App\Http\Controllers\PatientUploadController::class, 'destroy'])->name('patient.uploads.destroy');
Route::get('patient/uploads/{upload}/view', [\App\Http\Controllers\PatientUploadController::class, 'view'])->name('patient.uploads.view');
Route::get('patient/uploads/{upload}/download', [\App\Http\Controllers\PatientUploadController::class, 'download'])->name('patient.uploads.download');


// Pending patients
Route::resource('pendings', PendingPatientController::class)->middleware('admin.session');
Route::patch('pendings/approve/{id}', [PendingPatientController::class, 'approve'])
    ->middleware('admin.session')
    ->name('pendings.approve');

// Bulk operations for pending patients
Route::post('pendings/bulk/approve', [PendingPatientController::class, 'bulkApprove'])
    ->middleware('admin.session')
    ->name('pendings.bulk.approve');
Route::post('pendings/bulk/delete', [PendingPatientController::class, 'bulkDelete'])
    ->middleware('admin.session')
    ->name('pendings.bulk.delete');


// Download a consultation PDF
Route::get('patients/{patient}/consultations/{consultation}/download', [ConsultationController::class, 'downloadPdf'])
    ->middleware('admin.session')
    ->name('patients.consultations.download');

// Download all consultations for a patient as a single PDF
Route::get('patients/{patient}/consultations/download-all', [ConsultationController::class, 'downloadAll'])
    ->middleware('admin.session')
    ->name('patients.consultations.downloadAll');

// Nested resource routes for consultations under patients
Route::resource('patients.consultations', ConsultationController::class)->middleware('admin.session');



// Download patient PDF using Snappy (wkhtmltopdf) 
Route::get('patients/{id}/download-snappy', [PatientInfoController::class, 'downloadSnappy'])->name('patients.downloadSnappy');

// Securely serve patient signature from PRIVATE storage
Route::get('patients/{patient}/signature', [PatientInfoController::class, 'signature'])
    ->middleware('admin.session')
    ->name('patients.signature');


// Inventory (CRUD)
Route::resource('inventory', InventoryController::class)
    ->middleware('admin.session');

// Inventory report (PDF)
Route::get('admin/staff/inventory/report', [InventoryController::class, 'reportPdf'])
    ->middleware('admin.session')
    ->name('admin.inventory.report');

// Category management routes
Route::resource('categories', CategoryController::class)
    ->middleware('admin.session');

// Stock transaction routes
Route::post('/transactions/store', [StockTransactionController::class, 'store'])
    ->name('transactions.store');

// Admin settings and management
Route::middleware(['admin.session'])->group(function () {
    // Admin CRUD management
    Route::resource('admins', AdminController::class);
    // Custom route for role updates
    Route::patch('admins/{admin}/role', [AdminController::class, 'updateRole'])->name('admins.updateRole');

    // Student Accounts Management
    Route::get('accounts', [StudentAccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/{id}/edit', [StudentAccountController::class, 'edit'])->name('accounts.edit');
    Route::put('accounts/{id}', [StudentAccountController::class, 'update'])->name('accounts.update');
    Route::delete('accounts/{id}', [StudentAccountController::class, 'destroy'])->name('accounts.destroy');
    Route::post('accounts/bulk_destroy', [StudentAccountController::class, 'bulkDestroy'])->name('accounts.bulk_destroy');
});
