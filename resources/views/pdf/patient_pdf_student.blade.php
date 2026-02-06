<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Student Health Form</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        table {
            border-collapse: collapse;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-logo {
            width: 20%;
            text-align: center;
        }

        .header-logo img {
            width: 32px;
            height: auto;
        }

        .header-info {
            width: 60%;
            text-align: center;
        }

        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 0;
            padding: 0;
        }

        .school-address {
            font-size: 9pt;
            color: #2c5aa0;
            margin: 2px 0;
        }

        .school-unit {
            font-size: 10pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 5px 0 2px 0;
        }

        .form-title {
            font-size: 12pt;
            font-weight: bold;
            color: #2c5aa0;
            text-decoration: underline;
            margin: 5px 0 0 0;
        }

        .header-spacer {
            width: 20%;
        }

        .divider {
            border: none;
            border-top: 3px solid #2c5aa0;
            margin: 8px 0 10px 0;
        }

        .date-line {
            text-align: right;
            font-size: 9pt;
            color: #555;
            margin-bottom: 10px;
        }

        /* Content Box Styles - mPDF Compatible */
        .content-row {
            width: 100%;
            margin-bottom: 8px;
        }

        .content-row td {
            width: 50%;
            vertical-align: top;
            padding: 0 4px;
        }

        .info-box {
            width: 100%;
            border: 2px solid #2c5aa0;
            margin-bottom: 0;
        }

        .box-header {
            background-color: #2c5aa0;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            text-align: center;
            padding: 8px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            background-color: #f5f7fb;
        }

        .info-table tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .info-table td {
            padding: 6px 8px;
            font-size: 9pt;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #2c5aa0;
            background-color: #e8f0f8;
            width: 42%;
        }

        .info-value {
            color: #333;
            background-color: #f5f7fb;
            width: 58%;
        }

        /* List Styles */
        ul {
            margin: 0;
            padding-left: 18px;
            list-style-type: disc;
        }

        ul li {
            font-size: 9pt;
            margin: 2px 0;
        }

        /* Signature Image */
        .signature-img {
            max-width: 200px;
            max-height: 80px;
            border: 1px solid #ccc;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
        <tr>
            <td class="header-logo">
                <img src="{{ $logoUrl }}" alt="Logo" style="width: 100px; height: auto;">
            </td>
            <td class="header-info">
                <div class="school-name">CLINIC SYSTEM DEMO</div>
                <div class="school-address">ADDRESS OF THE CLINIC</div>
                <div class="school-unit">MEDICAL SERVICE UNIT (DEMO) </div>
                <div class="form-title">STUDENT HEALTH FORM</div>
            </td>
            <td class="header-spacer"></td>
        </tr>
    </table>

    <div style="text-align: center; border-top: 2px solid #207cca; margin-bottom: 10px;"></div>

    <div class="date-line">
        <strong>Date :</strong> {{ date('F j, Y') }}
    </div>

    <!-- Row 1: Student Info + Parent & Guardian Info -->
    <table class="content-row">
        <tr>
            <td>
                <table class="info-box">
                    <tr>
                        <td class="box-header" colspan="2">STUDENT'S INFORMATION</td>
                    </tr>
                </table>
                <table class="info-table">
                    @foreach([
                    'Last Name :' => 'last_name',
                    'First Name :' => 'first_name',
                    'Middle Name :' => 'middle_name',
                    'Suffix :' => 'suffix',
                    'Department :' => 'department',
                    'Course & Section :' => 'course',
                    'Year Level :' => 'year_level',
                    'Sex :' => 'sex',
                    'Age :' => 'age',
                    'Date of Birth :' => 'birthdate',
                    'Nationality :' => 'nationality',
                    'Religion :' => 'religion',
                    'Contact No :' => 'contact_no',
                    'Address :' => 'address'
                    ] as $label => $key)
                    <tr>
                        <td class="info-label" style="width:40%">{{ $label }}</td>
                        <td class="info-value" style="width:60%">{{ $patientData[$key] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td>
                <table class="info-box">
                    <tr>
                        <td class="box-header" colspan="2">PARENT'S & GUARDIAN'S INFORMATION</td>
                    </tr>
                </table>
                <table class="info-table">
                    @foreach([
                    "Mother's Name :" => 'mother_name',
                    "Mother Contact :" => 'mother_contact_no',
                    "Father's Name :" => 'father_name',
                    "Father Contact :" => 'father_contact_no',
                    "Guardian Name :" => 'guardian_name',
                    "Guardian Contact :" => 'guardian_contact_no',
                    "Relation to student :" => 'guardian_relationship',
                    "Guardian Address :" => 'guardian_address'
                    ] as $label => $key)
                    <tr>
                        <td class="info-label" style="width:40%">{{ $label }}</td>
                        <td class="info-value" style="width:60%">{{ $patientData[$key] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <!-- Row 2: Medical Info + Consent Info -->
    <table class="content-row">
        <tr>
            <td>
                <table class="info-box">
                    <tr>
                        <td class="box-header" colspan="2">MEDICAL INFORMATION</td>
                    </tr>
                </table>
                <table class="info-table">
                    @foreach([
                    'Allergies :' => 'allergies',
                    'Other Allergies :' => 'other_allergies',
                    'Treatment :' => 'treatments',
                    'COVID Status :' => 'covid',
                    'Flu Vaccine :' => 'flu_vaccine',
                    'Other Vaccine :' => 'other_vaccine',
                    'Medical History :' => 'medical_history',
                    'Medication :' => 'medication',
                    'Last Hospitalization :' => 'lasthospitalization'
                    ] as $label => $key)
                    <tr>
                        <td class="info-label" style="width:42%">{{ $label }}</td>
                        <td class="info-value" style="width:58%">{{ $patientData[$key] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
            <td>
                <table class="info-box">
                    <tr>
                        <td class="box-header" colspan="2">CONSENT INFORMATION</td>
                    </tr>
                </table>
                <table class="info-table">
                    <tr>
                        <td class="info-label" style="width:42%">Consent by :</td>
                        <td class="info-value" style="width:58%">{{ $patientData['consent_by'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Consent :</td>
                        <td class="info-value">
                            @if(!empty($patientData['consent']) && is_array($patientData['consent']))
                            <ul>
                                @foreach($patientData['consent'] as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            @else
                            —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label" style="width:42%">Signature :</td>
                        <td class="info-value" style="width:58%">
                            @if(!empty($patientData['signature']))
                            <img src="{{ $patientData['signature'] }}" alt="Signature" class="signature-img">
                            @else
                            —
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Computer Generated Message -->
    <div style="text-align: center; margin-top: 40px; margin-bottom: 20px; border-top: 2px solid #207cca; padding-top: 15px;">
        <p style="margin: 0; font-size: 11px; color: #1e5799; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">This is Computer Generated</p>
        <p style="margin: 5px 0 0 0; font-size: 9px; color: #888; font-style: italic;">Clinic Demo - Medical Service Unit Demo</p>
    </div>

</body>

</html>