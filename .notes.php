remove
student information - nationality and religion

guardian

medical information - All

consent - 5 checkboxes and consent by




create account with verification email
Route::post('student/register', [\App\Http\Controllers\RegistrationController::class, 'register']);

bypass verification
Route::post('student/register', [StudentAuthController::class, 'register']);



<link rel="icon" type="image/png" href="https://clinic.crisargawanon.site/images/logo2_pdf.png">
<link rel="icon" type="image/png" href="{{ asset('images/logo2_pdf.png') }}">