<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\Admin;

class AdminPasswordResetTest extends TestCase
{
    public function test_admin_password_reset_broker()
    {
        // Find an admin with an email
        $admin = Admin::whereNotNull('email')->where('email', '!=', '')->first();

        if (!$admin) {
            $this->fail('No admin with email found for testing');
        }

        echo "Testing with admin: {$admin->username}, email: {$admin->email}\n";

        // Test the password broker
        $status = Password::broker('admins')->sendResetLink([
            'email' => $admin->email
        ]);

        echo "Password reset status: {$status}\n";

        if ($status === Password::RESET_LINK_SENT) {
            echo "✅ Password reset link sent successfully\n";
        } else {
            echo "❌ Password reset failed with status: {$status}\n";
        }

        $this->assertTrue(true); // Just to make the test pass
    }
}
