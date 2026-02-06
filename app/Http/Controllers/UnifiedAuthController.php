<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\StudentAccount;
use Illuminate\Validation\ValidationException;

class UnifiedAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginIdentifier = $request->login_identifier;
        $password = $request->password;

        // 🔹 Try to authenticate as admin first (both username and email)
        $admin = Admin::where('username', $loginIdentifier)
            ->orWhere('email', $loginIdentifier)
            ->first();

        if ($admin) {
            if (Hash::check($password, $admin->password)) {
                Auth::guard('admin')->login($admin, $request->filled('remember'));

                // Set session variables for compatibility (including role)
                session([
                    'admin_id' => $admin->id,
                    'admin_username' => $admin->username,
                    'admin_role' => $admin->role,
                    'admin_firstname' => $admin->firstname,
                    'admin_lastname' => $admin->lastname,
                    'admin_authenticated' => true,
                    'user_type' => 'admin'
                ]);

                // Role-based redirects
                if (in_array($admin->role, ['admin', 'medical'])) {
                    return redirect()->intended('/admin/dashboard');
                } elseif ($admin->role === 'staff') {
                    return redirect()->intended('/inventory');
                } else {
                    return redirect()->intended('/admin/dashboard');
                }
            } else {
                // Admin exists but password is wrong
                throw ValidationException::withMessages([
                    'login_identifier' => ['password is incorrect.'],
                ]);
            }
        }

        // 🔹 If admin authentication fails, try student authentication
        $student = StudentAccount::where('email', $loginIdentifier)->first();

        if ($student) {
            // ✅ Check if account is inactive
            if ($student->status === 'inactive') {
                return back()->withErrors([
                    'login_identifier' => 'Your account is inactive. Please contact the administrator.',
                ]);
            }

            if (Hash::check($password, $student->password)) {
                // ✅ Update last login time
                $student->update([
                    'last_login_at' => now(),
                    'status' => 'active', // Reactivate if needed
                ]);

                // ✅ Create session for student
                session([
                    'student_id' => $student->id,
                    'student_email' => $student->email,
                    'student_authenticated' => true,
                    'user_type' => 'student'
                ]);

                return redirect()->intended('/student/dashboard');
            } else {
                // Student exists but password is wrong
                throw ValidationException::withMessages([
                    'login_identifier' => ['password is incorrect.'],
                ]);
            }
        }

        // ❌ No user found at all
        // Check if email exists in pending_registrations (not yet verified)
        $pending = \App\Models\PendingRegistration::where('email', $loginIdentifier)->first();
        if ($pending) {
            throw ValidationException::withMessages([
                'login_identifier' => [
                    'Your email is not verified yet. Please check your inbox for the verification link.'
                ],
            ]);
        }

        // No record found
        throw ValidationException::withMessages([
            'login_identifier' => [
                'No account found with this email.'
            ],  
        ]);
    }

    public function logout(Request $request)
    {
        // Clear Laravel Auth for both guards
        Auth::guard('admin')->logout();
        Auth::logout();

        // Clear all session data
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('message', 'You have been successfully logged out.');
    }
}
