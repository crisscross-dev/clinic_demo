<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== EXISTING ACCOUNTS ===\n";

// Check admin users
$users = App\Models\User::all();
echo "Admin Users:\n";
foreach ($users as $user) {
    echo "- Username: {$user->username} | Email: {$user->email}\n";
}

// Check student accounts  
$students = App\Models\StudentAccount::all();
echo "\nStudent Accounts:\n";
foreach ($students as $student) {
    echo "- Email: {$student->email} | Name: {$student->name}\n";
}
