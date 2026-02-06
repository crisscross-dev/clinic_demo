<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Handle a forgot password request for students.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('students')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'We have sent you a password reset link!'
            ]);
        }

        // Return consistent error format like Admin controller
        return response()->json([
            'message' => 'We cannot find a user with that email address.',
            'type' => 'user_not_found'
        ], 404);
    }
}
