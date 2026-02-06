Route::get('/test-admin-password-reset/{username}', function($username) {
// Find admin by username
$admin = App\Models\Admin::where('username', $username)->first();

if (!$admin) {
return "Admin not found with username: {$username}";
}

if (!$admin->email) {
return "Admin {$username} has no email address";
}

echo "Testing password reset for admin: {$admin->username}<br>";
echo "Admin email: {$admin->email}<br><br>";

try {
// Test the password broker directly
$status = Illuminate\Support\Facades\Password::broker('admins')->sendResetLink([
'email' => $admin->email
]);

echo "Password broker status: {$status}<br>";

if ($status === Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
return "<br>✅ SUCCESS: Password reset link sent!";
} else {
return "<br>❌ FAILED: Status = {$status}";
}

} catch (Exception $e) {
return "<br>❌ ERROR: " . $e->getMessage();
}
});