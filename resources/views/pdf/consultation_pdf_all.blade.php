<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>All Consultations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            width: 80px;
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

        .header-spacer {
            width: 20%;
        }

        .divider {
            border: none;
            border-top: 3px solid #2c5aa0;
            margin: 8px 0 10px 0;
        }

        strong {
            color: #2c5aa0;
        }

        .box {
            border: 2px solid #2c5aa0;
            padding: 10px;
            margin-bottom: 12px;
        }

        .box-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            border-bottom: 2px solid #2c5aa0;
            padding-bottom: 4px;
            color: #2c5aa0;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            table-layout: fixed;
        }

        .patient-table td {
            padding: 6px 8px;
            border: none;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            border-bottom: 1px solid #e4ecf9;
        }

        .patient-table .label {
            display: inline-block;
            width: 100px;
            font-weight: 700;
            color: #1e5799;
            white-space: nowrap;
        }

        .patient-table td.label-cell {
            width: 20%;
            font-weight: 700;
            color: #000000ff;
        }

        /* Vitals: consistent 4-column grid and inline labels */
        .vitals-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .vitals-table td {
            padding: 6px 8px;
            border: 1px solid #cddff5;
            font-size: 12.5px;
            vertical-align: top;
            word-break: break-word;
        }

        .vitals-table .label {
            position: static;
            display: inline-block;
            width: 120px;
            font-weight: 700;
            color: #1e5799;
            white-space: nowrap;
        }

        .box .section {
            margin-top: 6px;
        }

        .consultation {
            page-break-inside: avoid;
        }

        .consultation-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            color: #2c5aa0;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>

</head>

<body>

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ $logoUrl }}" alt="Logo" style="width: 100px; height: auto;">
            </td>
            <td class="header-info">
                <div class="school-name">CLINIC SYSTEM DEMO</div>
                <div class="school-address">ADDRESS OF THE CLINIC</div>
                <div class="school-unit">MEDICAL SERVICE UNIT (DEMO) </div>
            </td>
            <td class="header-spacer"></td>
        </tr>
    </table>

    <div style="text-align: center; border-top: 2px solid #207cca;"></div>

    <h2 class="consultation-title">CONSULTATION RECORDS</h2>

    <div class="box patient-info">
        <div class="box-title">PATIENT INFORMATION</div>
        <table class="patient-table">
            <tr>
                <td class="label-cell"><strong>Full Name:</strong></td>
                <td>{{ $patientData['last_name'] }}, {{ $patientData['first_name'] }}</td>
                <td class="label-cell"><strong>Course/Year:</strong></td>
                <td>{{ $patientData['year_level'] ?? '' }} {{ $patientData['course'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell"><strong>Contact Number:</strong></td>
                <td>{{ $patientData['contact_no'] ?? '—' }}</td>
                <td class="label-cell"><strong>Gender</strong></td>
                <td>{{ $patientData['sex'] ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell"><strong>Address:</strong></td>
                <td>{{ $patientData['address'] ?? '—' }}</td>
                <td class="label-cell"><strong>Age:</strong></td>
                <td>{{ $patientData['age'] ?? '—' }}</td>
            </tr>
        </table>
    </div>

    @foreach($consultations as $c)
    <div class="consultation">
        <div class="box">
            <div class="box-title">{{ $c['created_at'] }}</div>
            <table class="vitals-table">
                <tr>
                    <td colspan="4"><span class="label"><strong>Chief Complaint:</strong></span> {{ $c['chief_complaint'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td><span class="label"><strong>Temp:</strong></span> {{ $c['temperature'] ?: '—' }}</td>
                    <td><span class="label"><strong>BP:</strong></span> {{ $c['blood_pressure'] ?: '—' }}</td>
                    <td><span class="label"><strong>PR:</strong></span> {{ $c['pulse_rate'] ?: '—' }}</td>
                    <td><span class="label"><strong>RR:</strong></span> {{ $c['respiratory_rate'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td><span class="label"><strong>SpO₂:</strong></span> {{ $c['spo2'] ?: '—' }}</td>
                    <td><span class="label"><strong>Pain Scale:</strong></span> {{ $c['pain_scale'] ?: '—' }}</td>
                    <td><span class="label"><strong>LMP:</strong></span> {{ $c['lmp'] ?: '—' }}</td>
                    <td><span class="label"><strong>BP (repeat):</strong></span> {{ $c['blood_pressure'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><span class="label"><strong>Assessment:</strong></span> {{ $c['assessment'] ?: '—' }}</td>
                    <td colspan="2"><span class="label"><strong>Intervention:</strong></span> {{ $c['intervention'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td colspan="4"><span class="label"><strong>Medicine Given:</strong></span>
                        @php
                        $dispensedMedicines = $c['consultation_obj']->getDispensedMedicinesFromTransactions() ?? [];
                        // Group medicines by ID and sum quantities
                        $groupedMedicines = [];
                        if($dispensedMedicines && count($dispensedMedicines) > 0) {
                        foreach($dispensedMedicines as $med) {
                        $medId = $med['item_id'] ?? 'unknown';
                        $medName = $med['name'] ?? 'Unknown';
                        $medQuantity = $med['quantity'] ?? 0;

                        if(!isset($groupedMedicines[$medId])) {
                        $groupedMedicines[$medId] = [
                        'name' => $medName,
                        'total_quantity' => 0
                        ];
                        }

                        $groupedMedicines[$medId]['total_quantity'] += $medQuantity;
                        }
                        }
                        @endphp
                        @if(count($groupedMedicines) > 0)
                        @foreach($groupedMedicines as $med)
                        {{ $med['total_quantity'] }} {{ $med['name'] }}@if(!$loop->last); @endif
                        @endforeach
                        @else
                        —
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><span class="label"><strong>Outcome:</strong></span> {{ $c['outcome'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td colspan="4"><span class="label"><strong>Assessed by:</strong></span> {{ $c['assessed_by'] ?: '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach

    <!-- Computer Generated Message -->
    <div style="text-align: center; margin-top: 40px; margin-bottom: 20px; border-top: 2px solid #207cca; padding-top: 15px;">
        <p style="margin: 0; font-size: 11px; color: #1e5799; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">This is Computer Generated</p>
        <p style="margin: 5px 0 0 0; font-size: 9px; color: #888; font-style: italic;">Clinic Demo - Medical Service Unit Demo</p>
    </div>

</body>

</html>