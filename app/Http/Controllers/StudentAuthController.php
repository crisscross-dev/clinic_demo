<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\PatientInfo;


class StudentAuthController extends Controller
{
    public function showLoginForm()
    {
        return redirect()->route('login');
    }

    public function showRegistrationForm()
    {
        return view('student.register');
    }

    public function register(Request $request)
    {
        $request->validate([

            'first_name'            => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'last_name'             => 'required|string|max:255',

            'email' => 'required|string|email|max:255|unique:student_accounts',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $student = StudentAccount::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        PatientInfo::create([
            'student_account_id' => $student->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
        ]);

        // Registration successful, redirect to login page
        return redirect()->route('unified.login')->with('success', 'Account created successfully! Please log in.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $student = StudentAccount::where('email', $request->email)->first();

        if ($student && Hash::check($request->password, $student->password)) {
            session([
                'student_id' => $student->id,
                'student_email' => $student->email,
                'student_authenticated' => true
            ]);

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['student_id', 'student_email', 'student_authenticated']);
        return redirect()->route('student.login');
    }
}
