<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\Admin;
use App\Models\StudentAccount;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Determine if this is for student or admin based on route name
        $routeName = $request->route() ? $request->route()->getName() : '';
        $userType = str_contains($routeName ?? '', 'student') ? 'student' : 'admin';

        return view('admin.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email,
            'userType' => $userType,
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        // Debug logging
        Log::info('Password reset attempt', [
            'email' => $request->email,
            'token' => substr($request->token ?? '', 0, 10) . '...',
            'has_password' => !empty($request->password),
            'has_password_confirmation' => !empty($request->password_confirmation),
            'passwords_match' => $request->password === $request->password_confirmation,
            'route_name' => $request->route()->getName(),
        ]);

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/'
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters long.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*).',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Determine if this is for student or admin based on route name containing 'student'
        $routeName = $request->route() ? $request->route()->getName() : '';
        $userType = str_contains($routeName ?? '', 'student') ? 'student' : 'admin';
        $broker = $userType === 'student' ? 'students' : 'admins';
        // Redirect to unified login for now; change to 'student.login' if you want student-specific login
        $redirectRoute = 'admin.login';

        // Attempt to reset the user's password
        $response = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // Debug the password reset response
        Log::info('Password reset response', [
            'email' => $request->email,
            'response' => $response,
            'is_success' => $response == Password::PASSWORD_RESET,
            'broker' => $broker,
        ]);

        return $response == Password::PASSWORD_RESET
            ? redirect()->route($redirectRoute)->with('status', 'Your password has been reset!')
            : back()->withErrors(['email' => [__($response)]]);
    }
}
