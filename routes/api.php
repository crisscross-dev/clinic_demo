<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientInfoController;

Route::get('/test-api', function () {
    return response()->json(['message' => 'API route working']);
});

// Consent schedules management
Route::get('/consent-schedules', [PatientInfoController::class, 'getConsentSchedules']);
Route::post('/consent-schedules/{id}/deactivate', [PatientInfoController::class, 'deactivateConsentSchedule']);

// Consent forms status
Route::get('/consent-forms-status', [PatientInfoController::class, 'getConsentFormsStatus']);
