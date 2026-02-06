<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Models\StudentAccount;

class ForgotPasswordController extends Controller
{
    /**
     * Send a password reset link to the given admin email.
     * Always returns a generic success message to the client to avoid
     * leaking whether the email is registered.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|min:3',
        ], [
            'email.required' => 'Email or username is required.',
            'email.min' => 'Email or username must be at least 3 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'type' => 'validation_error'
            ], 422);
        }

        $input = trim($request->input('email')); // Could be email or username (admins)

        // Determine if this is an admin or student flow based on route name
        $routeName = $request->route() ? $request->route()->getName() : '';
        $userType = str_contains($routeName ?? '', 'student') ? 'student' : 'admin';
        $broker = $userType === 'student' ? 'students' : 'admins';

        // For admins we allow email or username lookup. For students we expect an email.
        if ($userType === 'admin') {
            $admin = \App\Models\Admin::where('email', $input)
                ->orWhere('username', $input)
                ->first();

            if (!$admin) {
                return response()->json([
                    'message' => 'Email or username not found. Please check your credentials and try again.',
                    'type' => 'user_not_found'
                ], 404);
            }

            $emailToSend = $admin->email;
        } else {
            // Student flow
            // Validate email format early
            if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Please provide a valid email address.',
                    'type' => 'validation_error'
                ], 422);
            }

            $student = StudentAccount::where('email', $input)->first();
            if (!$student) {
                return response()->json([
                    'message' => 'Email not found. Please check your credentials and try again.',
                    'type' => 'user_not_found'
                ], 404);
            }

            $emailToSend = $student->email;
        }

        if (!$emailToSend) {
            return response()->json([
                'message' => 'No email address associated with this account. Please contact the administrator.',
                'type' => 'no_email'
            ], 422);
        }

        // Send reset link using the appropriate broker
        try {
            Log::info('Attempting password reset', [
                'input' => $input,
                'email' => $emailToSend,
                'broker' => $broker,
                'user_type' => $userType,
            ]);

            $response = Password::broker($broker)->sendResetLink([
                'email' => $emailToSend
            ]);

            Log::info('Password reset response', [
                'input' => $input,
                'email' => $emailToSend,
                'response' => $response,
                'success' => $response === Password::RESET_LINK_SENT,
                'broker' => $broker,
            ]);

            if ($response === Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Password reset link has been sent successfully to your email address.',
                    'type' => 'success',
                    'email_hint' => $this->maskEmail($emailToSend)
                ], 200);
            } else {
                $errorMessage = $this->getPasswordBrokerErrorMessage($response);
                return response()->json([
                    'message' => $errorMessage,
                    'type' => 'send_failed'
                ], 500);
            }
        } catch (\Throwable $e) {
            Log::error('Forgot password send error', [
                'input' => $input,
                'email' => $emailToSend ?? 'N/A',
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'broker' => $broker,
            ]);

            return response()->json([
                'message' => 'Unable to send email. Please try again later or contact support.',
                'type' => 'system_error'
            ], 500);
        }
    }

    /**
     * Mask email address for privacy
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return '***@***.***';

        $username = $parts[0];
        $domain = $parts[1];

        $maskedUsername = strlen($username) > 2
            ? substr($username, 0, 2) . str_repeat('*', strlen($username) - 2)
            : str_repeat('*', strlen($username));

        $domainParts = explode('.', $domain);
        $maskedDomain = count($domainParts) > 1
            ? substr($domainParts[0], 0, 1) . str_repeat('*', max(1, strlen($domainParts[0]) - 1)) . '.' . end($domainParts)
            : str_repeat('*', strlen($domain));

        return $maskedUsername . '@' . $maskedDomain;
    }

    /**
     * Get user-friendly error message for password broker responses
     */
    private function getPasswordBrokerErrorMessage(string $response): string
    {
        switch ($response) {
            case Password::INVALID_USER:
                return 'We cannot find a user with that email address.';
            case Password::INVALID_TOKEN:
                return 'This password reset token is invalid.';
            case Password::RESET_THROTTLED:
                return 'Please wait before retrying. Too many reset requests.';
            default:
                return 'Unable to send password reset email. Please try again later.';
        }
    }
}
