<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAccount;
use App\Models\PatientInfo;
use App\Models\Consultation;
use App\Models\PatientUpload;
use App\Http\Controllers\PatientInfoController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class StudentDashboardController extends Controller
{
    /**
     * Download the student's profile as PDF (student side).
     */
    public function downloadProfilePdf(PatientInfo $patient)
    {
        $studentId = session('student_id');
        if (!$studentId || !session('student_authenticated')) {
            abort(401, 'Unauthorized access.');
        }
        if ($patient->student_account_id !== $studentId) {
            abort(403, 'Unauthorized access to profile.');
        }

        $fallback = fn($value) => empty($value) ? '—' : $value;
        // Consent: always array for PDF
        $consent = $patient->consent;
        if (!is_array($consent)) {
            $consent = is_string($consent) ? array_filter(array_map('trim', explode(',', $consent))) : [];
        }
        // Signature: embed as base64 for PDF compatibility
        $signatureUrl = null;
        $signatureDebug = null;
        $sig = $patient->signature;
        if (!empty($sig) && $sig !== '—') {
            if (str_starts_with($sig, 'iVBOR') || str_starts_with($sig, 'data:image')) {
                $signatureUrl = str_starts_with($sig, 'data:image') ? $sig : 'data:image/png;base64,' . $sig;
                $signatureDebug = [
                    'mode' => 'base64-inline',
                    'sig' => substr($sig, 0, 30),
                ];
            } else {
                $privatePath = storage_path('app/private/' . ltrim($sig, '/'));
                $publicPath  = storage_path('app/public/' . ltrim($sig, '/'));
                $filePath    = file_exists($privatePath) ? $privatePath : (file_exists($publicPath) ? $publicPath : null);
                $fileExists = $filePath && file_exists($filePath);
                $imageData = $fileExists ? file_get_contents($filePath) : null;
                $mimeType = null;
                $base64 = null;
                if ($fileExists && $imageData !== false && strlen($imageData) > 0) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $filePath);
                    finfo_close($finfo);
                    $base64 = base64_encode($imageData);
                    $signatureUrl = 'data:' . $mimeType . ';base64,' . $base64;
                }
                $signatureDebug = [
                    'mode' => 'file',
                    'privatePath' => $privatePath,
                    'publicPath' => $publicPath,
                    'filePath' => $filePath,
                    'fileExists' => $fileExists,
                    'mimeType' => $mimeType,
                    'base64_prefix' => $base64 ? substr($base64, 0, 30) : null,
                    'base64_length' => $base64 ? strlen($base64) : 0,
                ];
            }
        }
        $patientData = [
            'last_name' => $fallback($patient->last_name),
            'first_name' => $fallback($patient->first_name),
            'middle_name' => $fallback($patient->middle_name),
            'suffix' => $fallback($patient->suffix),
            'age' => $fallback($patient->age),
            'department' => $fallback($patient->department),
            'course' => $fallback($patient->course),
            'year_level' => $fallback($patient->year_level),
            'sex' => $fallback($patient->sex),
            'birthdate' => $fallback($patient->birthdate),
            'nationality' => $fallback($patient->nationality),
            'contact_no' => $fallback($patient->contact_no),
            'address' => $fallback($patient->address),
            'father_name' => $fallback($patient->father_name),
            'father_contact_no' => $fallback($patient->father_contact_no),
            'mother_name' => $fallback($patient->mother_name),
            'mother_contact_no' => $fallback($patient->mother_contact_no),
            'guardian_name' => $fallback($patient->guardian_name),
            'guardian_relationship' => $fallback($patient->guardian_relationship),
            'guardian_contact_no' => $fallback($patient->guardian_contact_no),
            'guardian_address' => $fallback($patient->guardian_address),
            'allergies' => $fallback($patient->allergies),
            'other_allergies' => $fallback($patient->other_allergies),
            'treatments' => $fallback($patient->treatments),
            'covid' => $fallback($patient->covid),
            'flu_vaccine' => $fallback($patient->flu_vaccine),
            'other_vaccine' => $fallback($patient->other_vaccine),
            'medical_history' => $fallback($patient->medical_history),
            'medication' => $fallback($patient->medication),
            'lasthospitalization' => $fallback($patient->lasthospitalization),
            'consent' => $consent,
            'consent_by' => $fallback($patient->consent_by),
            'signature' => $signatureUrl,
            'signatureDebug' => $signatureDebug,
        ];

        $formatted = [
            'birthdateStr' => $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('F j, Y') : '—',
            'createdStr' => $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('F j, Y - g:i A') : '—',
            'allergies' => $patient->allergies ?? '—',
            'treatments' => $patient->treatments ?? '—',
            'covid' => $patient->covid ?? '—',
            'consent' => $patient->consent ?? '—',
        ];

        // Logo URL for mPDF (base64)
        $logoPath = public_path('images/logo2_pdf.png');
        $logoUrl = '';
        if (file_exists($logoPath)) {
            try {
                $logoContent = file_get_contents($logoPath);
                if ($logoContent !== false) {
                    $logoBase64 = base64_encode($logoContent);
                    $logoUrl = 'data:image/png;base64,' . $logoBase64;
                }
            } catch (\Exception $e) {
                $logoUrl = '';
            }
        }

        $html = view('pdf/patient_pdf_student', [
            'patientData' => $patientData,
            'formatted' => $formatted,
            'logoUrl' => $logoUrl,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'tempDir' => sys_get_temp_dir(),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans'
        ]);
        $mpdf->WriteHTML($html);
        $safeSurname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->last_name);
        return response($mpdf->Output($safeSurname . '_profile.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $safeSurname . '_profile.pdf"');
    }
    public function index()
    {
        $studentId = session('student_id');
        $student = StudentAccount::find($studentId);

        if (!$student || !session('student_authenticated')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Get student's patient info with consent requests
        $patientInfo = PatientInfo::where('student_account_id', $studentId)
            ->with('consentRequests')
            ->first();

        // Get recent consultations - prioritize patient_id relationship
        $consultations = collect();

        if ($patientInfo) {
            // Primary method: Get consultations through patient_id - show more consultations
            $consultations = Consultation::where('patient_id', $patientInfo->id)
                ->orderBy('created_at', 'desc')
                ->take(10) // Increased from 5 to 10
                ->get();
        } else {
            // Fallback: Try direct student_account_id relationship
            $consultations = Consultation::where('student_account_id', $studentId)
                ->orderBy('created_at', 'desc')
                ->take(10) // Increased from 5 to 10
                ->get();
        }

        // Get uploaded files
        $uploads = PatientUpload::where('student_account_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if student has seen privacy policy
        $hasSeenPrivacyPolicy = $student->privacy_policy ?? false;

        return view('student.dashboard', compact('student', 'patientInfo', 'consultations', 'uploads', 'hasSeenPrivacyPolicy'));
    }

    public function profile()
    {
        $studentId = session('student_id');
        $student = StudentAccount::find($studentId);
        $patientInfo = PatientInfo::where('student_account_id', $studentId)->first();

        return view('student.profile', compact('student', 'patientInfo'));
    }

    public function consultations()
    {
        $studentId = session('student_id');
        $consultations = Consultation::where('student_account_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.consultations', compact('consultations'));
    }

    public function uploads()
    {
        $studentId = session('student_id');
        $uploads = PatientUpload::where('student_account_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.uploads', compact('uploads'));
    }

    /**
     * Student information endpoint - reuse PatientInfoController@show internally.
     */
    public function information()
    {
        $studentId = session('student_id');
        $student = StudentAccount::find($studentId);

        if (!$student || !session('student_authenticated')) {
            return redirect()->route('student.login');
        }

        $patient = PatientInfo::where('student_account_id', $studentId)->first();

        if (!$patient) {
            return redirect()->route('student.profile')->with('error', 'No profile information found.');
        }

        // Call the existing patient show action so we reuse view/data preparation.
        // Resolve a controller instance first to avoid static call errors and to allow
        // dependency injection on the controller's action method.
        $patientInfoController = app()->make(PatientInfoController::class);
        return app()->call([$patientInfoController, 'show'], ['id' => $patient->id]);
    }

    /**
     * Download a single consultation as PDF for authenticated student.
     */
    public function downloadConsultationPdf(\App\Models\Consultation $consultation)
    {
        $studentId = session('student_id');

        // Ensure student is authenticated
        if (!$studentId || !session('student_authenticated')) {
            abort(401, 'Unauthorized access.');
        }

        // Ensure the consultation belongs to a patient owned by the student
        $patient = $consultation->patient;
        if (!$patient || $patient->student_account_id !== $studentId) {
            abort(403, 'Unauthorized access to consultation.');
        }

        $fallback = fn($value) => empty($value) ? '—' : $value;

        // Patient data for display
        $patientData = [
            'last_name' => $fallback($patient->last_name),
            'first_name' => $fallback($patient->first_name),
            'year_level' => $patient->year_level ?? '',
            'course' => $patient->course ?? '—',
            'contact_no' => $fallback($patient->contact_no),
            'sex' => $fallback($patient->sex),
            'address' => $fallback($patient->address),
            'age' => $fallback($patient->age),
        ];

        // Formatted dates
        $formatted = [
            'createdStr' => $consultation->created_at ? \Carbon\Carbon::parse($consultation->created_at)->format('F j, Y - g:i A') : '—',
            'lmpStr' => $consultation->lmp ? \Carbon\Carbon::parse($consultation->lmp)->format('F j, Y') : '—',
        ];

        $val = function ($src, $key, $suffix = '') {
            $v = data_get($src, $key);
            return ($v !== null && $v !== '') ? ($v . $suffix) : '—';
        };

        // Logo URL for DomPDF/mPDF - use base64 encoding (most reliable)
        $logoPath = public_path('images/logo2_pdf.png');
        $logoUrl = '';

        if (file_exists($logoPath)) {
            try {
                $logoContent = file_get_contents($logoPath);
                if ($logoContent !== false) {
                    $logoBase64 = base64_encode($logoContent);
                    $logoUrl = 'data:image/png;base64,' . $logoBase64;
                }
            } catch (\Exception $e) {
                $logoUrl = '';
            }
        }

        // Generate HTML from view (reuse existing PDF blade)
        $html = view('pdf/consultation_pdf', [
            'patient' => $patient,
            'consultation' => $consultation,
            'patientData' => $patientData,
            'formatted' => $formatted,
            'val' => $val,
            'logoUrl' => $logoUrl,
        ])->render();

        // Create mPDF instance (same options as other controller)
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'tempDir' => sys_get_temp_dir(),
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans'
        ]);

        $mpdf->WriteHTML($html);

        $safeSurname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->last_name);

        // Download the PDF
        return response($mpdf->Output($safeSurname . '_consultation.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $safeSurname . '_consultation.pdf"');
    }

    public function updatePassword(Request $request)
    {
        $studentId = session('student_id');
        $student = StudentAccount::find($studentId);

        if (!$student || !session('student_authenticated')) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        // Validate the request
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()],
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $student->password = Hash::make($request->password);
        $student->save();

        return redirect()->route('student.profile')->with('password_updated', true);
    }

    public function signature(PatientInfo $patient)
    {
        $studentId = session('student_id');

        // Ensure student is authenticated
        if (!$studentId || !session('student_authenticated')) {
            abort(401, 'Unauthorized access.');
        }

        // Ensure the patient belongs to the authenticated student
        if ($patient->student_account_id !== $studentId) {
            abort(403, 'Unauthorized access to signature.');
        }

        // Check if signature exists
        if (!$patient->signature) {
            abort(404, 'Signature not found.');
        }

        // Try private storage first (correct location)
        if (Storage::disk('local')->exists($patient->signature)) {
            $path = Storage::disk('local')->path($patient->signature);
            return response()->file($path, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        // Fallback: try public storage (legacy)
        $publicPath = public_path('storage/' . $patient->signature);
        if (file_exists($publicPath)) {
            return response()->file($publicPath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        abort(404, 'Signature file not found.');
    }

    /**
     * Mark privacy policy as accepted for the student.
     */
    public function acceptPrivacyPolicy(Request $request)
    {
        $studentId = session('student_id');
        if (!$studentId || !session('student_authenticated')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        $student = StudentAccount::find($studentId);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student account not found.'
            ], 404);
        }

        $student->privacy_policy = true;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Privacy policy accepted successfully.'
        ]);
    }
}
