<?php

namespace App\Http\Controllers;

use App\Models\PendingRegistration;
use App\Mail\AccountVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(Request $request)
    {
        // Validate all input fields
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:pending_registrations,email',
                'unique:users,email',
                'unique:student_accounts,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#]).{8,}$/'
            ],
        ], [
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        // Only create pending registration AFTER all validations pass
        try {
            $token = Str::random(48);
            $pending = PendingRegistration::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'token' => $token,
                'token_expires_at' => now()->addDay(), // expires in 24 hours
            ]);

            $verificationUrl = route('verify.registration', ['token' => $token]);
            Mail::to($pending->email)->send(new AccountVerificationMail($pending->first_name, $verificationUrl));

            return redirect()->route('unified.login')->with([
                'swalMessage' => 'A verification link has been sent to your email. Please check your inbox to complete registration.',
                'swalTitle' => 'Check Your Email',
            ]);
        } catch (\Exception $e) {
            // If anything goes wrong, redirect back with error
            return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }

    public function verify($token)
    {
        $pending = PendingRegistration::where('token', $token)->first();
        if (!$pending) {
            return redirect()->route('unified.login')->withErrors(['token' => 'Invalid or expired verification link.']);
        }
        if ($pending->token_expires_at && now()->greaterThan($pending->token_expires_at)) {
            $pending->delete();
            return redirect()->route('unified.login')->withErrors(['token' => 'Invalid or expired verification link.']);
        }

        // Ensure email is unique in student_accounts
        if (\App\Models\StudentAccount::where('email', $pending->email)->exists()) {
            $pending->delete();
            return redirect()->route('unified.login')->withErrors(['email' => 'This email is already registered.']);
        }

        // Move to student_accounts table
        $student = \App\Models\StudentAccount::create([
            'first_name' => $pending->first_name,
            'middle_name' => $pending->middle_name,
            'last_name' => $pending->last_name,
            'name' => trim($pending->first_name . ' ' . ($pending->middle_name ? $pending->middle_name . ' ' : '') . $pending->last_name),
            'email' => $pending->email,
            'password' => $pending->password,
            'status' => 'active',
        ]);

        // Also create PatientInfo record
        \App\Models\PatientInfo::create([
            'student_account_id' => $student->id,
            'first_name' => $pending->first_name,
            'middle_name' => $pending->middle_name,
            'last_name' => $pending->last_name,
        ]);

        $pending->delete();

        return redirect()->route('unified.login')->with('success', 'Account verified successfully! Please log in.');
    }
}
