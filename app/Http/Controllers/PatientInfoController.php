<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\ConsentGrantedMail;
use App\Mail\ConsentDeniedMail;
// (removed duplicate use statements)

use Illuminate\Http\Request;
use App\Models\PatientInfo;
use Illuminate\Support\Facades\Auth;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;


class PatientInfoController extends Controller
{
    /**
     * Display consent management page with all patients.
     */
    public function consentRequests(Request $request) // consent table
    {
        $query = PatientInfo::query();

        // Eager load consent requests relationship
        $query->with('consentRequests');

        // Get all patients regardless of status for consent management
        $query->whereIn('status', ['approved', 'pending']);

        // ONLY show students who have requested consent access
        $query->where('consent_access_requested', true);

        // Filter by department
        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        // Search functionality
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%");
            });
        }

        // Filter by consent lock status
        if ($request->has('consent_status')) {
            $consentStatus = $request->input('consent_status');
            if ($consentStatus === 'locked') {
                $query->where('consent_form', true); // 1 (true) = locked
            } elseif ($consentStatus === 'unlocked') {
                $query->where('consent_form', false); // 0 (false) = unlocked
            }
        }

        $patients = $query->orderBy('last_name', 'asc')
            ->paginate(50)
            ->withQueryString();

        // Get unique departments for filter dropdown
        $departments = PatientInfo::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('patients.consent.consent_request', compact('patients', 'departments'));
    }



    /**
     * Toggle consent lock status for a patient.
     */
    public function toggleConsent(Request $request, PatientInfo $patient)
    {
        try {
            $request->validate([
                'consent_form' => 'required|boolean',
                'consent_access_requested' => 'sometimes|boolean', // Optional parameter for reject action
                'status' => 'sometimes|string|in:pending,granted,declined'
            ]);

            $accessRequestedBefore = $patient->consent_access_requested;

            $patient->consent_form = $request->input('consent_form');

            // If consent_access_requested is provided, use it; otherwise reset to false
            $patient->consent_access_requested = $request->input('consent_access_requested', false);

            $patient->save();

            // Persist status to the latest PatientConsent record for this patient, if provided.
            if ($request->filled('status')) {
                try {
                    $status = $request->input('status');

                    // Try to get the most recent consent request
                    $consent = \App\Models\PatientConsent::where('patient_info_id', $patient->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($consent) {
                        $consent->status = $status;
                        $consent->save();
                    } else {
                        // Create a new consent record if none exists (best-effort)
                        \App\Models\PatientConsent::create([
                            'patient_info_id' => $patient->id,
                            'consent_reason' => '',
                            'status' => $status,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to persist consent status: ' . $e->getMessage());
                }
            }

            $studentAccount = $patient->studentAccount;

            // Send email if consent is granted (unlocked)
            if ($patient->consent_form === false || $patient->consent_form === 0 || $patient->consent_form === '0') {
                if ($studentAccount && !empty($studentAccount->email)) {
                    try {
                        Mail::to($studentAccount->email)->send(new ConsentGrantedMail($patient));
                    } catch (\Exception $mailEx) {
                        Log::error('Failed to send consent granted email: ' . $mailEx->getMessage());
                    }
                }
            }
            // Send email if consent is denied (locked and access request was reset)
            elseif (($patient->consent_form === true || $patient->consent_form === 1 || $patient->consent_form === '1')
                && $accessRequestedBefore == true
                && $patient->consent_access_requested === false
            ) {
                if ($studentAccount && !empty($studentAccount->email)) {
                    try {
                        Mail::to($studentAccount->email)->send(new ConsentDeniedMail($patient));
                    } catch (\Exception $mailEx) {
                        Log::error('Failed to send consent denied email: ' . $mailEx->getMessage());
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Consent status updated successfully',
                'consent_form' => $patient->consent_form,
                'status' => $patient->consent_form ? 'locked' : 'unlocked' // true=locked, false=unlocked
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update consent status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk toggle consent lock status for all patients or specific department.
     */
    public function bulkToggleConsent(Request $request)
    {
        try {
            $request->validate([
                'consent_form' => 'required|boolean',
                'department' => 'nullable|string'
            ]);

            $lockStatus = $request->input('consent_form');
            $department = $request->input('department');

            // Build query
            $query = PatientInfo::query();

            // Filter by department if specified
            if (!empty($department)) {
                $query->where('department', $department);
            }

            // Update patient records
            $updatedCount = $query->update([
                'consent_form' => $lockStatus,
                'consent_access_requested' => false // Reset all access requests
            ]);

            $action = $lockStatus ? 'locked' : 'unlocked';
            $deptText = $department ? " for {$department}" : '';

            return response()->json([
                'success' => true,
                'message' => "Successfully {$action} {$updatedCount} consent form(s){$deptText}",
                'updated_count' => $updatedCount,
                'status' => $action,
                'department' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update consent forms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule consent form access for specific department and time range.
     */
    public function scheduleConsentAccess(Request $request)
    {
        try {
            $request->validate([
                'department' => 'nullable|string',
                'startTime' => 'required|date',
                'endTime' => 'required|date|after:startTime'
            ]);

            $department = $request->input('department');
            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');

            // Parse datetime in application timezone to ensure consistency
            $startTimeCarbon = \Carbon\Carbon::parse($startTime, config('app.timezone'));
            $endTimeCarbon = \Carbon\Carbon::parse($endTime, config('app.timezone'));

            // Log for debugging
            Log::info('Creating consent schedule', [
                'department' => $department ?: 'All departments',
                'start_time' => $startTimeCarbon->format('Y-m-d H:i:s'),
                'end_time' => $endTimeCarbon->format('Y-m-d H:i:s'),
                'timezone' => config('app.timezone')
            ]);

            // Store schedule in database
            $schedule = \App\Models\ConsentSchedule::create([
                'department' => $department,
                'start_time' => $startTimeCarbon,
                'end_time' => $endTimeCarbon,
                'is_active' => true,
                'created_by' => session('admin_id')
            ]);

            $deptText = $department ? " for {$department}" : '';

            return response()->json([
                'success' => true,
                'message' => "Consent form access scheduled{$deptText} from " .
                    $startTimeCarbon->format('M d, Y h:i A') . " to " .
                    $endTimeCarbon->format('M d, Y h:i A'),
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to schedule consent access: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule consent access: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all active consent schedules.
     */
    public function getConsentSchedules()
    {
        try {
            $schedules = \App\Models\ConsentSchedule::where('is_active', true)
                ->orderBy('start_time', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'schedules' => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate a consent schedule.
     */
    public function deactivateConsentSchedule($id)
    {
        try {
            $schedule = \App\Models\ConsentSchedule::findOrFail($id);
            $schedule->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Schedule deactivated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get consent forms status (locked/unlocked counts).
     */
    public function getConsentFormsStatus(Request $request)
    {
        try {
            $department = $request->input('department');

            $query = PatientInfo::query();

            if ($department) {
                $query->where('department', $department);
            }

            $lockedCount = (clone $query)->where('consent_form', true)->count();
            $unlockedCount = (clone $query)->where('consent_form', false)->count();

            return response()->json([
                'success' => true,
                'locked_count' => $lockedCount,
                'unlocked_count' => $unlockedCount,
                'department' => $department ?: 'All Departments'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Student requests access to edit consent section.
     */
    public function requestConsentAccess(Request $request)
    {
        try {
            // Validate the reason
            $request->validate([
                'reason' => 'required|string|min:20|max:500'
            ]);

            // Get current student's patient record
            $studentId = session('student_id');

            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student session not found'
                ], 401);
            }

            $patient = PatientInfo::where('student_account_id', $studentId)->first();

            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient record not found'
                ], 404);
            }

            // Check if already requested (true = already sent request)
            if ($patient->consent_access_requested === true) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already requested consent access',
                    'already_requested' => true
                ]);
            }

            // Record the request (true = request sent, will appear in admin table)
            $patient->consent_access_requested = true;
            $patient->save();

            // Save the consent reason
            \App\Models\PatientConsent::create([
                'patient_info_id' => $patient->id,
                'consent_reason' => $request->input('reason')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Consent access request submitted successfully. Admin will review your request.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid reason (20-500 characters)',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of all patients.
     */
    public function index(Request $request) // approved table
    {
        $query = PatientInfo::query();

        // Only show approved patients
        $query->where('status', 'approved');

        // Search across all relevant columns
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhere('year_level', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }
        // Always use pagination
        $patients = $query->orderBy('last_name', 'asc')
            ->paginate(50)
            ->withQueryString();

        $totalPatients = PatientInfo::where('status', 'approved')->count();

        $departments = PatientInfo::where('status', 'approved')
            ->distinct('department')
            ->whereNotNull('department')
            ->pluck('department')
            ->sort();

        return view('patients.index', compact(
            'patients',
            'totalPatients',
            'departments'
        ));
    }

    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                // Identity & demographics
                'first_name'            => 'required|string|max:255',
                'middle_name'           => 'nullable|string|max:255',
                'last_name'             => 'required|string|max:255',
                'suffix'                => 'nullable|string|max:10',
                'age'                   => 'nullable|integer|between:0,120',
                'sex'                   => 'nullable|string|max:10',
                'birthdate'             => 'nullable|date',
                'nationality'           => 'nullable|string|max:64',
                'religion'              => 'nullable|string|max:64',

                // Contact & address
                'contact_no'            => 'nullable|string|max:32',
                'address'               => 'nullable|string|max:255',

                // School-related
                'department'            => 'nullable|string|max:64',
                'course'                => 'nullable|string|max:255',
                'year_level'            => 'nullable|string|max:16',

                // Emergency / guardian
                'father_name'           => 'nullable|string|max:255',
                'mother_name'           => 'nullable|string|max:255',
                'guardian_name'         => 'nullable|string|max:255',
                'guardian_relationship' => 'nullable|string|max:255',
                'father_contact_no'     => 'nullable|string|max:32',
                'mother_contact_no'     => 'nullable|string|max:32',
                'guardian_contact_no'   => 'nullable|string|max:32',
                'guardian_address'      => 'nullable|string|max:255',

                // Medical info
                'allergies'             => 'nullable|array',
                'other_allergies'       => 'nullable|string|max:255',
                'treatments'            => 'nullable|array',
                'covid'                 => 'nullable|array',
                'flu_vaccine'           => 'nullable|string|max:255',
                'other_vaccine'         => 'nullable|string|max:255',
                'medical_history'       => 'nullable|string',
                'medication'            => 'nullable|string',
                'lasthospitalization'   => 'nullable|string',
                'consent'               => 'nullable|array',
                'consent_by'            => 'nullable|string|max:255',

                // signature (now base64 instead of file upload)
                'signature'             => 'nullable|string',

                // Status
                'status'                => 'nullable|in:pending,approved,rejected',

                // For updates
                'patient_id'            => 'nullable|integer|exists:patient_infos,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please check your form data. Some fields contain invalid information.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            // Helper to normalize n/a variations to null
            $normalizeToNull = function ($value) {
                if (empty($value)) return null;
                if (is_string($value)) {
                    $normalized = strtolower(trim($value));
                    if ($normalized === 'n/a' || $normalized === 'na') return null;
                }
                return $value;
            };

            // Convert arrays to strings for DB
            $data['allergies'] = isset($data['allergies']) ? implode(', ', $data['allergies']) : null;
            $data['treatments'] = isset($data['treatments']) ? implode(', ', $data['treatments']) : null;
            $data['covid'] = isset($data['covid']) ? implode(', ', $data['covid']) : null;
            $data['consent'] = isset($data['consent']) ? implode(', ', $data['consent']) : null;

            // Normalize all string fields to convert n/a to null
            $fieldsToNormalize = [
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'sex',
                'nationality',
                'religion',
                'contact_no',
                'address',
                'department',
                'course',
                'year_level',
                'father_name',
                'mother_name',
                'guardian_name',
                'guardian_relationship',
                'father_contact_no',
                'mother_contact_no',
                'guardian_contact_no',
                'guardian_address',
                'allergies',
                'other_allergies',
                'treatments',
                'covid',
                'flu_vaccine',
                'other_vaccine',
                'medical_history',
                'medication',
                'lasthospitalization',
                'consent',
                'consent_by'
            ];

            foreach ($fieldsToNormalize as $field) {
                if (isset($data[$field])) {
                    $data[$field] = $normalizeToNull($data[$field]);
                }
            }

            // Get authenticated student ID first (we need it to check existing patient)
            $studentId = session('student_id');

            if (!$studentId || !session('student_authenticated')) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Student authentication required'], 401);
                }
                return redirect()->route('login')->with('error', 'Please log in as a student');
            }

            // Check if patient record already exists (we need this before processing signature)
            $existingPatient = PatientInfo::where('student_account_id', $studentId)->first();

            // ✅ Handle signature base64 -> file (PRIVATE storage)
            // Delete old signature if new one is being uploaded
            if (!empty($data['signature']) && strpos($data['signature'], 'data:image') === 0) {
                // Delete old signature file if exists
                if ($existingPatient && !empty($existingPatient->signature)) {
                    // Check if old signature is a file path (not a base64 or URL)
                    if (
                        strpos($existingPatient->signature, 'data:image') !== 0 &&
                        strpos($existingPatient->signature, 'http') !== 0
                    ) {
                        // Delete the old signature file from storage
                        if (Storage::disk('local')->exists($existingPatient->signature)) {
                            Storage::disk('local')->delete($existingPatient->signature);
                            Log::info('Deleted old signature: ' . $existingPatient->signature);
                        }
                    }
                }

                // Save new signature
                $imageParts = explode(';base64,', $data['signature']);
                $imageBase64 = base64_decode($imageParts[1] ?? '');
                $fileName = uniqid('sig_') . '.png';
                $relativePath = 'signatures/' . $fileName; // stored relative path on private disk

                // Store on private disk (storage/app/private)
                Storage::disk('local')->put($relativePath, $imageBase64, 'private');

                // Save only the relative path in DB
                $data['signature'] = $relativePath;
                Log::info('Saved new signature: ' . $relativePath);
            }

            // Remove patient_id from data array since it's not a database field
            $patientId = $data['patient_id'] ?? null;
            unset($data['patient_id']);

            // Only set status to 'pending' if it's a new record OR if current status is not 'approved'
            // If already approved, preserve the approved status when editing
            if ($existingPatient && $existingPatient->status === 'approved') {
                // Preserve approved status for approved patients
                $data['status'] = 'approved';
            } else {
                // Set to pending for new submissions or non-approved records
                $data['status'] = 'pending';
            }

            // Always update existing data or create new - student submits to replace existing data
            $patient = PatientInfo::updateOrCreate(
                ['student_account_id' => $studentId], // Find by student ID
                $data // Replace with new data (status is now preserved if approved)
            );

            // Lock consent form if student has submitted consent data
            // consent_form = 1 (true) means LOCKED
            if (!empty($data['consent']) && !empty($data['consent_by']) && !empty($data['signature'])) {
                $patient->consent_form = true; // Lock the consent form (1 = locked)
                $patient->save();
            }

            // Check if this was an update or create for appropriate message
            $isUpdate = !$patient->wasRecentlyCreated;
            $successMessage = $isUpdate
                ? 'Health information updated successfully! Your profile changes have been saved.'
                : 'Health information submitted successfully! Your profile is now complete.';

            if ($request->route()->getName() === 'patient.submit') {
                // Handle AJAX requests
                if ($request->ajax()) {
                    // Set flash message in session for when page reloads
                    session()->flash('success', $successMessage);

                    return response()->json([
                        'success' => true,
                        'message' => $successMessage,
                        'redirect' => route('student.dashboard')
                    ]);
                }

                // Check if user is authenticated as student and redirect to dashboard
                if (Auth::guard('web')->check()) {
                    return redirect()->route('student.dashboard')
                        ->with('success', $successMessage);
                }

                return redirect()->route('home')
                    ->with('success', 'Your health information has been submitted successfully! We will review your information and contact you soon.');
            }

            return redirect()->route('patients.index')->with('success', 'Student patient created successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving your information. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'An error occurred while saving your information. Please try again.']);
        }
    }

    /**
     * Display the specified patient.
     */
    public function show($id)
    {
        $patient = PatientInfo::findOrFail($id);
        // Fetch consultations newest-first
        $consultations = $patient->consultations()->orderBy('created_at', 'desc')->get();

        // Helper to fallback to '—'
        $fallback = fn($value) => empty($value) ? '—' : $value;

        // List of patient fields
        $fields = [
            'last_name',
            'first_name',
            'middle_name',
            'suffix',
            'age',
            'department',
            'course',
            'year_level',
            'sex',
            'nationality',
            'religion',
            'contact_no',
            'address',
            'father_name',
            'father_contact_no',
            'mother_name',
            'mother_contact_no',
            'guardian_name',
            'guardian_relationship',
            'guardian_contact_no',
            'guardian_address',
            'other_allergies',
            'flu_vaccine',
            'other_vaccine',
            'medical_history',
            'medication',
            'lasthospitalization',
            'consent_by',
            'signature'
        ];

        // Build for fallback and fields
        $patientData = [];
        foreach ($fields as $field) {
            $patientData[$field] = $field === 'signature'
                ? $patient->$field                // keep raw for image
                : $fallback($patient->$field);    // apply fallback
        }

        // Helper for formatting lists
        $formatList = function ($value) {
            $value = is_string($value) ? trim($value) : '';
            if ($value === '') return '—';

            $items = array_filter(array_map('trim', explode(',', $value)));
            if (!$items) return '—';

            $safeItems = array_map(function ($i) {
                return e($i);
            }, $items);
            return '<ul class="bullet-list"><li>' . implode('</li><li>', $safeItems) . '</li></ul>';
        };

        // Prepare all formatted values
        $formatted = [
            'birthdateStr' => $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('F j, Y') : '—',
            'createdStr' => $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('F j, Y - g:i A') : '—',
            'updatedStr' => $patient->updated_at ? \Carbon\Carbon::parse($patient->updated_at)->format('F j, Y - g:i A') : '—',
            'allergies' => $formatList($patient->allergies),
            'treatments' => $formatList($patient->treatments),
            'covid' => $formatList($patient->covid),
            'consent' => $formatList($patient->consent),
        ];

        // Get medicine data for consultation modal
        $inventoryItems = \App\Models\InventoryItem::with('category')
            ->orderBy('name')
            ->get();

        // Prepare clean medicine data for JavaScript (no linting issues)
        $medicinesForJs = $inventoryItems->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'stock' => $item->total_stock
            ];
        });

        return view('patients.show', compact('patientData', 'patient', 'formatted', 'consultations', 'medicinesForJs'));
    }




    /**
     * Show the form for editing the specified patient.
     */
    public function edit(PatientInfo $patient)
    {

        $fields = [
            'last_name',
            'first_name',
            'middle_name',
            'suffix',
            'age',
            'sex',
            'department',
            'birthdate',
            'course',
            'year_level',
            'religion',
            'nationality',
            'contact_no',
            'address',

            'father_name',
            'father_contact_no',
            'mother_name',
            'mother_contact_no',
            'guardian_name',
            'guardian_relationship',
            'guardian_contact_no',
            'guardian_address',

            'other_allergies',
            'flu_vaccine',
            'other_vaccine',

            'medical_history',
            'medication',
            'lasthospitalization',

        ];

        // Prepare values for the form
        $fieldValues = [];
        foreach ($fields as $field) {
            $value = old($field, data_get($patient, $field));

            // Format birthdate for date input
            if ($field === 'birthdate' && !empty($value)) {
                $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
            }

            $fieldValues[$field] = $value ?? '';
        }

        // Helper: Convert comma-separated strings into arrays
        function normalizeArray($val)
        {
            if (is_array($val)) {
                return array_map('trim', $val);
            }

            if (empty($val) || !is_string($val)) {
                return [];
            }

            return array_map('trim', explode(',', $val));
        }

        // Multi-checkbox fields
        $multiCheckboxFields = ['allergies', 'treatments', 'covid'];

        $selFields = [];
        foreach ($multiCheckboxFields as $field) {
            $selFields[$field] = normalizeArray(old($field, data_get($patient, $field)));
        }


        // Options arrays
        $allergyOptions = [
            ['value' => 'Food', 'label' => 'Food'],
            ['value' => 'Dust', 'label' => 'Dust'],
            ['value' => 'Heat', 'label' => 'Heat'],
            ['value' => 'Rhinitis', 'label' => 'Allergic Rhinitis'],
            ['value' => 'Drugs', 'label' => 'Drugs'],
            ['value' => 'None', 'label' => 'None'],
        ];

        $treatmentOptions = [
            ['value' => 'Antihistamine', 'label' => 'Antihistamine'],
            ['value' => 'None', 'label' => 'None'],
        ];

        $covidOptions = [
            ['value' => '1st dose', 'label' => '1st dose'],
            ['value' => '2nd dose', 'label' => '2nd dose'],
            ['value' => '1st booster', 'label' => '1st booster'],
            ['value' => '2nd booster', 'label' => '2nd booster'],
            ['value' => 'Not vaccinated', 'label' => 'Not Vaccinated'],
        ];

        $sexOptions = ['Male', 'Female'];
        // Department options
        $departmentOptions = [
            'BED - JHS',
            'BED - SHS',
            'HED - BSOA',
            'HED - BSCPE',
            'HED - BSP',
            'HED - BSA/MA',
            'FACULTY',
            'NTS',
        ];

        return view('patients.edit', compact(
            'patient',
            'allergyOptions',
            'treatmentOptions',
            'covidOptions',
            'departmentOptions',
            'fieldValues',
            'sexOptions',
            'selFields',


        ));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, PatientInfo $patient)
    {
        $data = $request->validate([
            // Identity & demographics
            'first_name'            => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'last_name'             => 'required|string|max:255',
            'sex'                   => 'nullable|string|max:10',
            'suffix'                => 'nullable|string|max:10',
            'age'                   => 'nullable|integer|min:0',
            'birthdate'             => 'nullable|date',
            'nationality'           => 'nullable|string|max:64',
            'religion'              => 'nullable|string|max:64',

            // Contact & address
            'contact_no'            => 'nullable|string|max:32',
            'address'               => 'nullable|string|max:255',

            // School-related
            'department'            => 'nullable|string|max:64',
            'course'                => 'nullable|string|max:255',
            'year_level'            => 'nullable|string|max:16',

            // Emergency / guardian
            'father_name'           => 'nullable|string|max:255',
            'mother_name'           => 'nullable|string|max:255',
            'guardian_name'         => 'nullable|string|max:255',
            'guardian_relationship' => 'nullable|string|max:255',
            'father_contact_no'     => 'nullable|string|max:32',
            'mother_contact_no'     => 'nullable|string|max:32',
            'guardian_contact_no'   => 'nullable|string|max:32',
            'guardian_address'      => 'nullable|string|max:255',

            // Medical info
            'allergies'             => 'nullable|array',
            'other_allergies'       => 'nullable|string|max:255',
            'treatments'            => 'nullable|array',
            'covid'                 => 'nullable|array',
            'flu_vaccine'           => 'nullable|string|max:255',
            'other_vaccine'         => 'nullable|string|max:255',
            'medical_history'       => 'nullable|string',
            'medication'            => 'nullable|string',
            'lasthospitalization'   => 'nullable|string',


            // Status
            'status'                => 'nullable|in:pending,approved,rejected',
        ]);

        // Helper to normalize n/a variations to null
        $normalizeToNull = function ($value) {
            if (empty($value)) return null;
            if (is_string($value)) {
                $normalized = strtolower(trim($value));
                if ($normalized === 'n/a' || $normalized === 'na') return null;
            }
            return $value;
        };

        // Convert arrays
        $data['allergies'] = isset($data['allergies']) ? implode(', ', $data['allergies']) : null;
        $data['treatments'] = isset($data['treatments']) ? implode(', ', $data['treatments']) : null;
        $data['covid']      = isset($data['covid']) ? implode(', ', $data['covid']) : null;

        // Normalize all string fields to convert n/a to null
        $fieldsToNormalize = [
            'first_name',
            'middle_name',
            'last_name',
            'suffix',
            'sex',
            'nationality',
            'religion',
            'contact_no',
            'address',
            'department',
            'course',
            'year_level',
            'father_name',
            'mother_name',
            'guardian_name',
            'guardian_relationship',
            'father_contact_no',
            'mother_contact_no',
            'guardian_contact_no',
            'guardian_address',
            'allergies',
            'other_allergies',
            'treatments',
            'covid',
            'flu_vaccine',
            'other_vaccine',
            'medical_history',
            'medication',
            'lasthospitalization',
            'consent_by'
        ];

        foreach ($fieldsToNormalize as $field) {
            if (isset($data[$field])) {
                $data[$field] = $normalizeToNull($data[$field]);
            }
        }

        // ✅ Handle signature replacement (PRIVATE storage)
        if ($request->hasFile('signature')) {
            if ($patient->signature) {
                Storage::disk('local')->delete($patient->signature);
            }

            $path = $request->file('signature')->store('signatures', 'local');
            $data['signature'] = $path;
        }

        $patient->update($data);

        return redirect()->route('patients.edit', $patient)
            ->with('success', 'Student patient updated successfully!');
    }


    /**
     * Remove the specified patient from storage.
     */
    public function destroy(PatientInfo $patient)
    {
        if ($patient->signature) {
            Storage::disk('local')->delete($patient->signature);
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Student patient deleted successfully!');
    }




    public function downloadSnappy($id)
    {
        $patient = PatientInfo::findOrFail($id);

        $safeSurname  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->last_name);
        $safeFirstname = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->first_name);

        // Logo URL for DomPDF - use base64 encoding (most reliable)
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
                // Logo loading failed, will show fallback in template
                $logoUrl = '';
            }
        }

        // Signature handling
        $sig = $patient->signature;
        $signatureUrl = null;

        if (!empty($sig)) {
            // Base64 signature
            if (str_starts_with($sig, 'iVBOR') || str_starts_with($sig, 'data:image')) {
                $signatureUrl = str_starts_with($sig, 'data:image') ? $sig : 'data:image/png;base64,' . $sig;
            } else {
                // Local storage file - convert to base64
                $privatePath = storage_path('app/private/' . ltrim($sig, '/'));
                $publicPath  = storage_path('app/public/' . ltrim($sig, '/'));
                $filePath    = file_exists($privatePath) ? $privatePath : (file_exists($publicPath) ? $publicPath : null);

                if ($filePath && file_exists($filePath)) {
                    $imageData = base64_encode(file_get_contents($filePath));
                    $signatureUrl = 'data:image/png;base64,' . $imageData;
                }
            }
        }

        // Patient data
        $patientData = [
            'created_at' => $patient->created_at?->format('F j, Y') ?? '—',
            'last_name' => $patient->last_name ?? '—',
            'first_name' => $patient->first_name ?? '—',
            'middle_name' => $patient->middle_name ?? '—',
            'suffix' => $patient->suffix ?? '—',
            'department' => $patient->department ?? '—',
            'course' => $patient->course ?? '—',
            'section' => $patient->section ?? '—',
            'year_level' => $patient->year_level ?? '—',
            'sex' => $patient->sex ?? '—',
            'age' => $patient->age ?? '—',
            'birthdate' => $patient->birthdate?->format('F j, Y') ?? '—',
            'nationality' => $patient->nationality ?? '—',
            'religion' => $patient->religion ?? '—',
            'contact_no' => $patient->contact_no ?? '—',
            'address' => $patient->address ?? '—',
            'mother_name' => $patient->mother_name ?? '—',
            'mother_contact_no' => $patient->mother_contact_no ?? '—',
            'father_name' => $patient->father_name ?? '—',
            'father_contact_no' => $patient->father_contact_no ?? '—',
            'guardian_name' => $patient->guardian_name ?? '—',
            'guardian_contact_no' => $patient->guardian_contact_no ?? '—',
            'guardian_relationship' => $patient->guardian_relationship ?? '—',
            'guardian_address' => $patient->guardian_address ?? '—',
            'allergies' => $patient->allergies ?? '—',
            'other_allergies' => $patient->other_allergies ?? '—',
            'treatments' => $patient->treatments ?? '—',
            'covid' => $patient->covid ?? '—',
            'flu_vaccine' => $patient->flu_vaccine ?? '—',
            'other_vaccine' => $patient->other_vaccine ?? '—',
            'medical_history' => $patient->medical_history ?? '—',
            'medication' => $patient->medication ?? '—',
            'lasthospitalization' => $patient->lasthospitalization ?? '—',
            'consent_by' => $patient->consent_by ?? '—',
            'consent' => array_filter(array_map('trim', is_array($patient->consent) ? $patient->consent : explode(',', $patient->consent ?? ''))),
            'signature' => $signatureUrl,
        ];

        // Generate HTML from view
        $html = view('pdf/patient_pdf', [
            'patientData' => $patientData,
            'logoUrl' => $logoUrl,
        ])->render();

        // Create mPDF instance with better CSS support
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

        // Write HTML content
        $mpdf->WriteHTML($html);

        // Download the PDF
        return response($mpdf->Output($safeSurname . '_' . $safeFirstname . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $safeSurname . '_' . $safeFirstname . '.pdf"');
    }



    /**
     * Serve the patient's signature from PRIVATE storage.
     * This route should be protected by auth/admin middleware.
     */
    public function signature(PatientInfo $patient)
    {
        if (!$patient->signature) {
            abort(404, 'Signature not found.');
        }

        $sigPath = ltrim($patient->signature, '/');
        $fullPath = storage_path('app/private/' . $sigPath);

        if (file_exists($fullPath)) {
            return response()->file($fullPath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        abort(404, 'Signature file not found at: ' . $fullPath);
    }
}
