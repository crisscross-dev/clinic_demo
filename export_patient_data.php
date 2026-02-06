<?php

/**
 * Export Patient Data Script
 * This script displays all patient-related data from the local database
 * for manual transfer to the online system.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PatientInfo;
use App\Models\Consultation;
use App\Models\PatientConsent;
use App\Models\PatientUpload;
use App\Models\PendingRegistration;

// Set display mode (json or html)
$displayMode = $_GET['mode'] ?? 'html';

// Get all patients with their related data
$patients = PatientInfo::with(['consultations', 'consentRequests', 'patientUploads', 'studentAccount'])->get();
$pendingRegistrations = PendingRegistration::all();

if ($displayMode === 'json') {
    // JSON output for programmatic use
    header('Content-Type: application/json');
    echo json_encode([
        'patients' => $patients,
        'pending_registrations' => $pendingRegistrations,
        'summary' => [
            'total_patients' => $patients->count(),
            'total_consultations' => Consultation::count(),
            'total_consents' => PatientConsent::count(),
            'total_uploads' => PatientUpload::count(),
            'total_pending' => $pendingRegistrations->count(),
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

// HTML output for easy reading
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Data Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }

        h2 {
            color: #555;
            margin-top: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 8px;
        }

        h3 {
            color: #666;
            margin-top: 20px;
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
        }

        .summary {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
            font-weight: bold;
        }

        .patient-card {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
            margin: 10px 0;
        }

        .info-item {
            padding: 8px;
            background: white;
            border-radius: 3px;
            position: relative;
            padding-right: 35px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            margin-right: 5px;
        }

        .copy-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 14px;
            padding: 4px;
            border-radius: 3px;
            transition: all 0.2s;
        }

        .copy-icon:hover {
            background: #e9ecef;
            color: #007bff;
        }

        .copy-icon.copied {
            color: #28a745;
        }

        .consultation-card {
            background: #fff3cd;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 3px solid #ffc107;
        }

        .consent-card {
            background: #d1ecf1;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 3px solid #17a2b8;
        }

        .upload-card {
            background: #d4edda;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 3px solid #28a745;
        }

        .pending-card {
            background: #f8d7da;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 3px solid #dc3545;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 10px 10px 0;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
        }

        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }

        .no-data {
            color: #999;
            font-style: italic;
        }

        .copy-btn {
            display: inline-block;
            padding: 5px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }

        .copy-btn:hover {
            background: #0056b3;
        }

        .copy-btn.copied {
            background: #28a745;
        }
    </style>
    <script>
        function copyToClipboard(text, btnId) {
            navigator.clipboard.writeText(text).then(function() {
                const btn = document.getElementById(btnId);
                const originalText = btn.textContent;
                btn.textContent = '✓ Copied!';
                btn.classList.add('copied');
                setTimeout(function() {
                    btn.textContent = originalText;
                    btn.classList.remove('copied');
                }, 2000);
            }).catch(function(err) {
                alert('Failed to copy: ' + err);
            });
        }

        function copyFieldValue(text, iconId) {
            if (!text || text === 'N/A') return;
            navigator.clipboard.writeText(text).then(function() {
                const icon = document.getElementById(iconId);
                const originalText = icon.textContent;
                icon.textContent = '✓';
                icon.classList.add('copied');
                setTimeout(function() {
                    icon.textContent = originalText;
                    icon.classList.remove('copied');
                }, 1500);
            }).catch(function(err) {
                console.error('Failed to copy:', err);
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>📋 Patient Data Export - Local Database</h1>

        <div>
            <a href="?mode=html" class="btn">HTML View</a>
            <a href="?mode=json" class="btn btn-success">JSON Export</a>
        </div>

        <div class="summary">
            <h3>📊 Summary</h3>
            <div class="summary-item">👥 Total Patients: <?= $patients->count() ?></div>
            <div class="summary-item">📝 Total Consultations: <?= Consultation::count() ?></div>
            <div class="summary-item">✅ Total Consents: <?= PatientConsent::count() ?></div>
            <div class="summary-item">📎 Total Uploads: <?= PatientUpload::count() ?></div>
            <div class="summary-item">⏳ Pending Registrations: <?= $pendingRegistrations->count() ?></div>
        </div>

        <!-- Patients Section -->
        <h2>👥 Patients Data (<?= $patients->count() ?>)</h2>
        <?php
        // Helper function to create copyable field
        function copyableField($label, $value, $uniqueId)
        {
            $displayValue = htmlspecialchars($value ?? 'N/A');
            $copyValue = $value ?? 'N/A';
            return '<div class="info-item">
                        <span class="info-label">' . $label . ':</span> ' . $displayValue . '
                        <span class="copy-icon" id="copy-' . $uniqueId . '" onclick="copyFieldValue(\'' . htmlspecialchars($copyValue, ENT_QUOTES) . '\', \'copy-' . $uniqueId . '\')">📋</span>
                    </div>';
        }

        // Helper function to format birthdate as "YYYY, Mon, DD"
        function formatBirthdate($date)
        {
            if (!$date) return 'N/A';
            try {
                $dateObj = new DateTime($date);
                return $dateObj->format('Y, M, d');
            } catch (Exception $e) {
                return $date;
            }
        }
        ?>
        <?php if ($patients->isEmpty()): ?>
            <p class="no-data">No patient data found.</p>
        <?php else: ?>
            <?php foreach ($patients as $index => $patient): ?>
                <div class="patient-card">
                    <h3>Patient #<?= $index + 1 ?>: <?= htmlspecialchars($patient->first_name . ' ' . ($patient->middle_name ? $patient->middle_name . ' ' : '') . $patient->last_name . ($patient->suffix ? ' ' . $patient->suffix : '')) ?></h3>

                    <?php if ($patient->studentAccount): ?>
                        <h4>🔐 Account Information</h4>
                        <div class="info-grid">
                            <?= copyableField('First Name', $patient->first_name, 'firstname-' . $patient->id) ?>
                            <?= copyableField('Middle Name', $patient->middle_name, 'middlename-' . $patient->id) ?>
                            <?= copyableField('Last Name', $patient->last_name, 'lastname-' . $patient->id) ?>
                            <?= copyableField('Email', $patient->studentAccount->email, 'email-' . $patient->id) ?>
                        </div>
                    <?php endif; ?>

                    <h4>👨‍🎓 Student Information</h4>
                    <div class="info-grid">
                        <?= copyableField('Department', $patient->department, 'department-' . $patient->id) ?>
                        <?= copyableField('Course', $patient->course, 'course-' . $patient->id) ?>
                        <?= copyableField('Year Level', $patient->year_level, 'year-' . $patient->id) ?>
                        <?= copyableField('Last Name', $patient->last_name, 'lastname-' . $patient->id) ?>
                        <?= copyableField('First Name', $patient->first_name, 'firstname-' . $patient->id) ?>
                        <?= copyableField('Middle Name', $patient->middle_name, 'middlename-' . $patient->id) ?>
                        <?= copyableField('Suffix', $patient->suffix, 'suffix-' . $patient->id) ?>
                        <?= copyableField('Age', $patient->age, 'age-' . $patient->id) ?>
                        <?= copyableField('Sex', $patient->sex, 'sex-' . $patient->id) ?>
                        <?= copyableField('Birthdate', formatBirthdate($patient->birthdate), 'birthdate-' . $patient->id) ?>
                        <?= copyableField('Nationality', $patient->nationality, 'nationality-' . $patient->id) ?>
                        <?= copyableField('Religion', $patient->religion, 'religion-' . $patient->id) ?>
                        <?= copyableField('Contact No', $patient->contact_no, 'contact-' . $patient->id) ?>
                        <?= copyableField('Address', $patient->address, 'address-' . $patient->id) ?>
                    </div>

                    <h4>👨‍👩‍👧 Parent / Guardian Information</h4>
                    <div class="info-grid">
                        <?= copyableField('Father Name', $patient->father_name, 'father-name-' . $patient->id) ?>
                        <?= copyableField('Father Contact', $patient->father_contact_no, 'father-contact-' . $patient->id) ?>
                        <?= copyableField('Mother Name', $patient->mother_name, 'mother-name-' . $patient->id) ?>
                        <?= copyableField('Mother Contact', $patient->mother_contact_no, 'mother-contact-' . $patient->id) ?>
                        <?= copyableField('Guardian Name', $patient->guardian_name, 'guardian-name-' . $patient->id) ?>
                        <?= copyableField('Guardian Contact', $patient->guardian_contact_no, 'guardian-contact-' . $patient->id) ?>
                        <?= copyableField('Guardian Relationship', $patient->guardian_relationship, 'guardian-rel-' . $patient->id) ?>
                        <?= copyableField('Guardian Address', $patient->guardian_address, 'guardian-address-' . $patient->id) ?>
                    </div>

                    <!-- <h4>💊 Medical Information</h4>
                    <div class="info-grid">
                        <?= copyableField('Allergies', $patient->allergies, 'allergies-' . $patient->id) ?>
                        <?= copyableField('Other Allergies', $patient->other_allergies, 'other-allergies-' . $patient->id) ?>
                        <?= copyableField('Treatments', $patient->treatments, 'treatments-' . $patient->id) ?>
                        <?= copyableField('COVID', $patient->covid, 'covid-' . $patient->id) ?>
                        <?= copyableField('Flu Vaccine', $patient->flu_vaccine, 'flu-' . $patient->id) ?>
                        <?= copyableField('Other Vaccine', $patient->other_vaccine, 'other-vaccine-' . $patient->id) ?>
                        <?= copyableField('Medical History', $patient->medical_history, 'medical-history-' . $patient->id) ?>
                        <?= copyableField('Medication', $patient->medication, 'medication-' . $patient->id) ?>
                        <?= copyableField('Last Hospitalization', $patient->lasthospitalization, 'hospitalization-' . $patient->id) ?>
                    </div> -->

                    <h4>✅ Consent Information</h4>
                    <div class="info-grid">
                        <?= copyableField('Consent By', $patient->consent_by, 'consent-by-' . $patient->id) ?>
                    </div>

                    <?php if ($patient->signature): ?>
                        <h4>📝 Signature</h4>
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 3px;">
                            <strong>🔑 Password Hash (Database Value):</strong>
                            <button class="copy-btn" id="copy-pwd-<?= $patient->id ?>" onclick="copyToClipboard('<?= htmlspecialchars($patient->studentAccount->password ?? '', ENT_QUOTES) ?>', 'copy-pwd-<?= $patient->id ?>')">📋 Copy Hash</button>
                            <br>
                            <code id="pwd-hash-<?= $patient->id ?>" style="font-size: 10px; word-break: break-all; display: block; padding: 8px; background: white; border: 1px solid #ddd; margin-top: 5px;">
                                <?= htmlspecialchars($patient->studentAccount->password ?? 'N/A') ?>
                            </code>
                            <small style="color: #856404; display: block; margin-top: 5px;">⚠️ This is a hashed password (bcrypt), not the actual password.</small>
                        </div>
                        <div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                            <div style="margin-bottom: 10px;">
                                <strong>Signature File:</strong>
                                <span style="color: #28a745;">patient_<?= $patient->id ?>_signature.png</span>
                                <a href="<?= htmlspecialchars($patient->signature) ?>" download="patient_<?= $patient->id ?>_signature.png" style="margin-left: 10px; color: #007bff; text-decoration: none;">⬇ Download PNG</a>
                            </div>
                            <div style="margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 3px;">
                                <strong>Database Value:</strong><br>
                                <code style="font-size: 11px; word-break: break-all; display: block; padding: 8px; background: white; border: 1px solid #ddd; margin-top: 5px;">
                                    <?= htmlspecialchars($patient->signature) ?>
                                </code>
                            </div>
                            <img src="<?= htmlspecialchars($patient->signature) ?>" alt="Patient Signature" style="max-width: 400px; border: 1px solid #ccc; padding: 10px; background: white;">
                            <details style="margin-top: 10px;">
                                <summary><strong>Copy Base64 Data</strong></summary>
                                <textarea readonly onclick="this.select();" style="width: 100%; height: 100px; font-family: monospace; font-size: 11px; padding: 5px;"><?= htmlspecialchars($patient->signature) ?></textarea>
                                <small style="color: #666;">Click textarea to select all, then Ctrl+C to copy</small>
                            </details>
                        </div>
                    <?php endif; ?>

                    <!-- Consultations -->
                    <?php if ($patient->consultations && $patient->consultations->count() > 0): ?>
                        <h4>📝 Consultations (<?= $patient->consultations->count() ?>)</h4>
                        <?php foreach ($patient->consultations as $consultation): ?>
                            <div class="consultation-card">
                                <strong>Date:</strong> <?= $consultation->created_at->format('Y-m-d H:i:s') ?><br>
                                <strong>Assessed By:</strong> <?= htmlspecialchars($consultation->assessed_by ?? 'N/A') ?><br>
                                <strong>Chief Complaint:</strong> <?= htmlspecialchars($consultation->chief_complaint ?? 'N/A') ?><br>
                                <strong>Vitals:</strong>
                                Temp: <?= $consultation->temperature ?? 'N/A' ?>°C,
                                BP: <?= htmlspecialchars($consultation->blood_pressure ?? 'N/A') ?>,
                                PR: <?= $consultation->pulse_rate ?? 'N/A' ?>,
                                RR: <?= $consultation->respiratory_rate ?? 'N/A' ?>,
                                SpO2: <?= $consultation->spo2 ?? 'N/A' ?>%<br>
                                <?php if ($consultation->lmp): ?>
                                    <strong>LMP:</strong> <?= $consultation->lmp ?><br>
                                <?php endif; ?>
                                <?php if ($consultation->pain_scale): ?>
                                    <strong>Pain Scale:</strong> <?= $consultation->pain_scale ?><br>
                                <?php endif; ?>
                                <strong>Assessment:</strong> <?= htmlspecialchars($consultation->assessment ?? 'N/A') ?><br>
                                <strong>Intervention:</strong> <?= htmlspecialchars($consultation->intervention ?? 'N/A') ?><br>
                                <strong>Outcome:</strong> <?= htmlspecialchars($consultation->outcome ?? 'N/A') ?><br>
                                <?php if ($consultation->dispensed_medicines): ?>
                                    <strong>Dispensed Medicines:</strong>
                                    <pre><?= json_encode($consultation->dispensed_medicines, JSON_PRETTY_PRINT) ?></pre>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No consultations for this patient.</p>
                    <?php endif; ?>

                    <!-- Consents -->
                    <?php if ($patient->consentRequests && $patient->consentRequests->count() > 0): ?>
                        <h4>✅ Consent Requests (<?= $patient->consentRequests->count() ?>)</h4>
                        <?php foreach ($patient->consentRequests as $consent): ?>
                            <div class="consent-card">
                                <strong>Reason:</strong> <?= htmlspecialchars($consent->consent_reason ?? 'N/A') ?><br>
                                <strong>Status:</strong> <?= htmlspecialchars($consent->status ?? 'N/A') ?><br>
                                <strong>Created:</strong> <?= $consent->created_at->format('Y-m-d H:i:s') ?><br>
                                <strong>Updated:</strong> <?= $consent->updated_at->format('Y-m-d H:i:s') ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Uploads -->
                    <?php if ($patient->patientUploads && $patient->patientUploads->count() > 0): ?>
                        <h4>📎 Uploads (<?= $patient->patientUploads->count() ?>)</h4>
                        <?php foreach ($patient->patientUploads as $upload): ?>
                            <div class="upload-card">
                                <strong>File Name:</strong> <?= htmlspecialchars($upload->original_filename ?? 'N/A') ?><br>
                                <strong>Type:</strong> <?= htmlspecialchars($upload->file_type ?? 'N/A') ?><br>
                                <strong>Size:</strong> <?= number_format($upload->file_size / 1024, 2) ?> KB<br>
                                <strong>Path:</strong> <?= htmlspecialchars($upload->file_path ?? 'N/A') ?><br>
                                <strong>Uploaded:</strong> <?= $upload->created_at->format('Y-m-d H:i:s') ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <details>
                        <summary><strong>📄 Raw JSON Data</strong></summary>
                        <pre><?= json_encode($patient->toArray(), JSON_PRETTY_PRINT) ?></pre>
                    </details>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Pending Registrations Section -->
        <h2>⏳ Pending Registrations (<?= $pendingRegistrations->count() ?>)</h2>
        <?php if ($pendingRegistrations->isEmpty()): ?>
            <p class="no-data">No pending registrations found.</p>
        <?php else: ?>
            <?php foreach ($pendingRegistrations as $pending): ?>
                <div class="pending-card">
                    <strong>Email:</strong> <?= htmlspecialchars($pending->email ?? 'N/A') ?><br>
                    <strong>Token:</strong> <?= htmlspecialchars($pending->token ?? 'N/A') ?><br>
                    <strong>Created:</strong> <?= $pending->created_at ? $pending->created_at->format('Y-m-d H:i:s') : 'N/A' ?><br>
                    <strong>Expires:</strong> <?= $pending->expires_at ? $pending->expires_at->format('Y-m-d H:i:s') : 'N/A' ?>
                    <details>
                        <summary><strong>Full Data</strong></summary>
                        <pre><?= json_encode($pending->toArray(), JSON_PRETTY_PRINT) ?></pre>
                    </details>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>

</html>