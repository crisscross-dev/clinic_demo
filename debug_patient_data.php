<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Get all student accounts with their patient info
$students = \App\Models\StudentAccount::with('patientInfo')->get();

echo "=== DEBUG: Student Accounts and Patient Info ===\n";

foreach ($students as $student) {
    echo "Student ID: {$student->id}\n";
    echo "Email: {$student->email}\n";
    echo "Patient Info: " . ($student->patientInfo ? "EXISTS (ID: {$student->patientInfo->id})" : "NONE") . "\n";

    if ($student->patientInfo) {
        echo "  - Name: {$student->patientInfo->first_name} {$student->patientInfo->last_name}\n";
        echo "  - Department: {$student->patientInfo->department}\n";
        echo "  - Course: {$student->patientInfo->course}\n";
        echo "  - Student Account ID: {$student->patientInfo->student_account_id}\n";
    }
    echo "---\n";
}

// Also check patient info without relationship
echo "\n=== All Patient Info Records ===\n";
$patients = \App\Models\PatientInfo::all();

foreach ($patients as $patient) {
    echo "Patient ID: {$patient->id}\n";
    echo "Name: {$patient->first_name} {$patient->last_name}\n";
    echo "Student Account ID: {$patient->student_account_id}\n";
    echo "Department: {$patient->department}\n";
    echo "---\n";
}
