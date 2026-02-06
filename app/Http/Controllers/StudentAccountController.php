<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class StudentAccountController extends Controller
{
    /**
     * Display a listing of student accounts.
     */
    public function index(Request $request)
    {
        // Base query: join with patientInfo
        $query = StudentAccount::with('patientInfo')
            ->leftJoin('patient_infos', 'student_accounts.id', '=', 'patient_infos.student_account_id')
            ->select('student_accounts.*');

        // 🟢 Step 1: Show only APPROVED patient info records first
        $query->where('patient_infos.status', 'approved');

        // 🟢 Step 2: Apply active/inactive filter (default: active)
        $status = $request->input('status', 'active');
        if ($status !== 'all') {
            $query->where('student_accounts.status', $status);
        }

        // 🔍 Step 3: Apply search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_accounts.email', 'like', "%{$search}%")
                    ->orWhereHas('patientInfo', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('course', 'like', "%{$search}%")
                            ->orWhere('department', 'like', "%{$search}%");
                    });
            });
        }

        // 🧾 Step 4: Sorting and pagination
        $accounts = $query
            ->orderBy('patient_infos.last_name', 'asc')
            ->orderBy('patient_infos.first_name', 'asc')
            ->orderBy('student_accounts.email', 'asc')
            ->paginate(50);

        return view('patients.accounts.account_list', compact('accounts', 'status'));
    }


    /**
     * Show the form for editing the specified student account.
     */
    public function edit($id)
    {
        $account = StudentAccount::with('patientInfo')->findOrFail($id);
        return response()->json([
            'success' => true,
            'account' => [
                'id' => $account->id,
                'email' => $account->email,
                'full_name' => $account->full_name,
                'course' => $account->patientInfo->course ?? 'N/A',
                'year_level' => $account->patientInfo->year_level ?? 'N/A',
                'department' => $account->patientInfo->department ?? 'N/A',
            ]
        ]);
    }

    /**
     * Update the specified student account.
     */
    public function update(Request $request, $id)
    {
        $account = StudentAccount::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:student_accounts,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $account->email = $request->email;

        if ($request->filled('password')) {
            $account->password = Hash::make($request->password);
        }

        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully'
        ]);
    }

    /**
     * Remove the specified student account.
     */
    public function destroy($id)
    {
        $account = StudentAccount::findOrFail($id);
        $fullName = $account->full_name;

        // Get patient info to access their folder and signature
        $patientInfo = $account->patientInfo;

        if ($patientInfo) {
            $patientId = $patientInfo->id;

            // Delete uploaded files folder and all contents
            $uploadsPath = storage_path("app/private/patient_uploads/{$patientId}");
            if (File::exists($uploadsPath)) {
                File::deleteDirectory($uploadsPath);
            }

            // Delete signature file if exists
            if (!empty($patientInfo->signature)) {
                $signaturePath = $patientInfo->signature; // e.g., "signatures/sig_xxxxx.png"
                if (Storage::disk('local')->exists($signaturePath)) {
                    Storage::disk('local')->delete($signaturePath);
                }
            }
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => "Account for {$fullName} has been deleted successfully"
        ]);
    }

    /**
     * Bulk delete student accounts.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'account_ids' => 'required|array',
            'account_ids.*' => 'exists:student_accounts,id',
        ]);

        $accounts = StudentAccount::with('patientInfo')->whereIn('id', $request->account_ids)->get();
        $count = 0;

        foreach ($accounts as $account) {
            $patientInfo = $account->patientInfo;

            if ($patientInfo) {
                $patientId = $patientInfo->id;

                // Delete uploaded files folder and all contents
                $uploadsPath = storage_path("app/private/patient_uploads/{$patientId}");
                if (File::exists($uploadsPath)) {
                    File::deleteDirectory($uploadsPath);
                }

                // Delete signature file if exists
                if (!empty($patientInfo->signature)) {
                    $signaturePath = $patientInfo->signature; // e.g., "signatures/sig_xxxxx.png"
                    if (Storage::disk('local')->exists($signaturePath)) {
                        Storage::disk('local')->delete($signaturePath);
                    }
                }
            }

            $account->delete();
            $count++;
        }

        return redirect()->route('accounts.index')
            ->with('success', "{$count} account(s) deleted successfully");
    }
}
