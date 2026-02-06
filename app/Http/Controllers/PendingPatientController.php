<?php

namespace App\Http\Controllers;

use App\Models\PatientInfo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PatientApprovedMail;
use App\Mail\PatientDeniedMail;

class PendingPatientController extends Controller
{

    // Show only pending patients with search & department filter
    public function index(Request $request)
    {
        $query = PatientInfo::query();

        // Only patients that have a department (not null or empty)
        $query->whereNotNull('department')
            ->where('department', '!=', '')
            // Only pending patients
            ->where('status', 'pending');

        // Search
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

        // Get pending patients sorted by last name
        $pendingPatients = $query
            ->orderBy('created_at', 'desc') // newest first
            ->orderBy('last_name', 'asc')   // then sort by last name
            ->paginate(50)
            ->withQueryString();


        // Counts and department list
        $totalPatients = PatientInfo::where('status', 'pending')->count();
        $todayRegistrations = PatientInfo::where('status', 'pending')
            ->whereDate('created_at', today())
            ->count();
        $departments = PatientInfo::where('status', 'pending')
            ->whereNotNull('department')
            ->distinct()
            ->orderBy('department', 'asc')
            ->pluck('department');

        return view('patients.pendings.index', compact(
            'pendingPatients',
            'totalPatients',
            'todayRegistrations',
            'departments'
        ));
    }

    public function show($id)
    {
        // ensure we only fetch pending patients
        $patient = PatientInfo::where('status', 'pending')
            ->where('id', $id)
            ->firstOrFail();

        $fallback = function ($value) {
            return empty($value) ? '—' : $value;
        };

        $bdRaw = data_get($patient, 'birthdate');
        $birthdateStr = $bdRaw ? Carbon::parse((string) $bdRaw)->format('F j, Y') : null;

        $createdRaw = data_get($patient, 'created_at');
        $updatedRaw = data_get($patient, 'updated_at');
        $createdStr = $createdRaw ? Carbon::parse((string) $createdRaw)->format('F j, Y - g:i A') : null;
        $updatedStr = $updatedRaw ? Carbon::parse((string) $updatedRaw)->format('F j, Y - g:i A') : null;



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
            'nationality' => $fallback($patient->nationality),
            'religion' => $fallback($patient->religion),
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
            'other_allergies' => $fallback($patient->other_allergies),
            'flu_vaccine' => $fallback($patient->flu_vaccine),
            'other_vaccine' => $fallback($patient->other_vaccine),
            'medical_history' => $fallback($patient->medical_history),
            'medication' => $fallback($patient->medication),
            'lasthospitalization' => $fallback($patient->lasthospitalization),
            'consent_by' => $fallback($patient->consent_by),
            'signature' => $patient->signature, // raw path/value (kept for compatibility)
        ];


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

        // Signature helper: determine if patient has a signature and prepare a URL for it
        $signaturePath = data_get($patient, 'signature');
        $hasSignature = !empty($signaturePath);
        $signatureUrl = $hasSignature ? route('patients.signature', ['patient' => $patient->id]) : null;

        return view('patients.pendings.show', compact(
            'patient',
            'birthdateStr',
            'createdStr',
            'updatedStr',
            'patientData',
            'formatted',
            'fallback',
            'formatList',
            'hasSignature',
            'signatureUrl'

        ));
    }

    // Approve a pending patient
    public function approve($id)
    {
        $patient = PatientInfo::findOrFail($id);
        $patient->status = 'approved';
        $patient->save();

        // Send approval email if student account has email
        if ($patient->studentAccount && $patient->studentAccount->email) {
            try {
                Mail::to($patient->studentAccount->email)->send(new PatientApprovedMail($patient));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        // Redirect back to the pending list
        return redirect()->route('pendings.index')
            ->with('success', 'Patient approved successfully.');
    }

    public function destroy($pending)
    {
        $patient = PatientInfo::findOrFail($pending);

        // Send denial email if student account has email
        if ($patient->studentAccount && $patient->studentAccount->email) {
            try {
                Mail::to($patient->studentAccount->email)->send(new PatientDeniedMail($patient));
            } catch (\Exception $e) {
                Log::error('Failed to send denial email: ' . $e->getMessage());
            }
        }

        // Set status to rejected and unlock consent form so student can edit and resubmit
        $patient->status = 'rejected';
        $patient->consent_form = false; // Unlock the consent form
        $patient->save();

        return redirect()->route('pendings.index')->with('success', 'Student patient form rejected. Student can resubmit.');
    }

    // Bulk approve selected patients
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:patient_infos,id'
        ]);

        $patients = PatientInfo::whereIn('id', $request->patient_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($patients as $patient) {
            $patient->status = 'approved';
            $patient->save();

            // Send approval email
            if ($patient->studentAccount && $patient->studentAccount->email) {
                try {
                    Mail::to($patient->studentAccount->email)->send(new PatientApprovedMail($patient));
                } catch (\Exception $e) {
                    Log::error('Failed to send bulk approval email: ' . $e->getMessage());
                }
            }
        }

        $count = $patients->count();

        return redirect()->route('pendings.index')
            ->with('success', "Successfully approved {$count} patient(s).");
    }

    // Bulk delete selected patients
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'patient_ids' => 'required|array',
            'patient_ids.*' => 'exists:patient_infos,id'
        ]);

        $patients = PatientInfo::whereIn('id', $request->patient_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($patients as $patient) {
            // Send denial email
            if ($patient->studentAccount && $patient->studentAccount->email) {
                try {
                    Mail::to($patient->studentAccount->email)->send(new PatientDeniedMail($patient));
                } catch (\Exception $e) {
                    Log::error('Failed to send bulk denial email: ' . $e->getMessage());
                }
            }

            // Set status to rejected and unlock consent form so student can edit
            $patient->status = 'rejected';
            $patient->consent_form = false; // Unlock the consent form
            $patient->save();
        }

        $count = $patients->count();

        return redirect()->route('pendings.index')
            ->with('success', "Successfully rejected {$count} patient(s). They can resubmit.");
    }
}
