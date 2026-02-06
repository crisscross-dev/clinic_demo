<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consultations Report</title>
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

        .form-title {
            font-size: 12pt;
            font-weight: bold;
            color: #2c5aa0;
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

        .date-submitted {
            text-align: right;
            font-size: 9pt;
            color: #555;
            margin: 10px 0 15px;
        }

        .consultation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .consultation-table th {
            background-color: #3168bbff;
            color: white;
            padding: 8px 6px;
            font-size: 9pt;
            font-weight: bold;
            text-align: left;
            border: 1px solid #3168bbff;
        }

        .consultation-table td {
            padding: 6px;
            font-size: 9pt;
            border: 1px solid #ccc;
            vertical-align: top;
            word-wrap: break-word;
        }

        .consultation-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .name-column {
            width: 21%;
        }

        .course-column {
            width: 17%;
        }

        .date-column {
            width: 12%;
        }

        .complaint-column {
            width: 20%;
        }

        .medicine-column {
            width: 15%;
        }

        .outcome-column {
            width: 15%;
        }

        .no-consultations {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .summary-box {
            background-color: #e8f0f8;
            border: 2px solid #2c5aa0;
            padding: 10px;
            margin-top: 15px;
            font-size: 10pt;
        }

        .summary-box h4 {
            color: #2c5aa0;
            font-size: 11pt;
            margin: 0 0 8px 0;
        }

        .filter-info {
            background-color: #f5f7fb;
            border: 2px solid #2c5aa0;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        .filter-info strong {
            color: #2c5aa0;
        }


        .student-consultation {
            background-color: #fafbfc;
        }

        .consultation-table td.empty-cell {
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }

        .computer-generated {
            text-align: center;
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            font-style: italic;
            border-top: 2px solid #2c5aa0;
            padding-top: 10px;
        }

        /* Medicine List Styles */
        .medicine-list {
            margin: 0;
            padding-left: 12px;
            list-style-type: disc;
        }

        .medicine-list li {
            font-size: 8pt;
            margin: 1px 0;
            line-height: 1.3;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ $logoUrl }}" alt="Logo" style="width: 100px; height: auto;">
            </td>
            <td class="header-info">
                <div class="school-name">CLINIC SYSTEM DEMO</div>
                <div class="school-address">ADDRESS OF THE CLINIC</div>
                <div class="school-unit">MEDICAL SERVICE UNIT (DEMO) </div>
                <div class="form-title">CONSULTATION RECORDS REPORT</div>
            </td>
            <td class="header-spacer"></td>
        </tr>
    </table>

    <div style="text-align: center; border-top: 2px solid #207cca;"></div>

    <div class="date-submitted">
        <strong>Generated Date:</strong> {{ now()->format('F j, Y') }}
    </div>

    <!-- Filter Information -->
    <!-- @if(isset($dateRange) || isset($department) || isset($course))
    <div class="filter-info">
        <strong>Report Filters:</strong>
        @if(isset($dateRange))
        {{ $dateRange }} |
        @endif
        @if(isset($department))
        Department: {{ $department }} |
        @endif
        @if(isset($course))
        Course: {{ $course }} |
        @endif
        Total Records: {{ collect($consultations)->sum(function($group) { return count($group['consultations']); }) }}
    </div>
    @endif -->

    <!-- Consultations Table -->
    @if(!empty($consultations) && count($consultations) > 0)
    <table class="consultation-table">
        <thead>
            <tr>
                <th class="name-column">Student Name</th>
                <th class="course-column">Course & Section</th>
                <th class="date-column">Date</th>
                <th class="complaint-column">Chief Complaint</th>
                <th class="medicine-column">Medicine Given</th>
                <th class="outcome-column">Outcome</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultations as $studentGroup)
            @foreach($studentGroup['consultations'] as $index => $consultation)
            <tr class="{{ $index === 0 ? 'student-group-header' : 'student-consultation' }}">
                <td class="name-column {{ $index !== 0 ? 'empty-cell' : '' }}">
                    @if($index === 0)
                    {{ $studentGroup['student_name'] ?? '—' }}
                    @endif
                </td>
                <td class="course-column {{ $index !== 0 ? 'empty-cell' : '' }}">
                    @if($index === 0)
                    {{ $studentGroup['course'] ?? '—' }}
                    @endif
                </td>
                <td class="date-column">
                    {{ $consultation['date'] ?? '—' }}
                </td>
                <td class="complaint-column">
                    {{ $consultation['chief_complaint'] ?? '—' }}
                </td>
                <td class="medicine-column">
                    @php
                    $consultationObj = \App\Models\Consultation::find($consultation['id'] ?? 0);
                    $dispensedMedicines = $consultationObj ? $consultationObj->getDispensedMedicinesFromTransactions() : [];
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
                    @if(count($groupedMedicines) > 1)
                    <ul class="medicine-list">
                        @foreach($groupedMedicines as $med)
                        <li>{{ $med['total_quantity'] }}x {{ $med['name'] }}</li>
                        @endforeach
                    </ul>
                    @else
                    @foreach($groupedMedicines as $med)
                    {{ $med['total_quantity'] }}x {{ $med['name'] }}
                    @endforeach
                    @endif
                    @else
                    —
                    @endif
                </td>
                <td class="outcome-column">
                    {{ $consultation['outcome'] ?? '—' }}
                </td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
    <!-- Summary -->
    <div class="summary-box" style="font-size:11pt; margin-bottom: 10px;">
        <h4 style="color:#2c5aa0; font-size:11pt; margin:0 0 0 0; text-align:center">CONSULTATION SUMMARY</h4>
        <hr style="border: none; border-top: 2.5px solid #207cca !important; margin: 5px 0 12px 0;">

        <table style="width:100%; border:none; font-size:11pt;">
            <tr>
                <td style="width:38%; color:#2c5aa0; vertical-align: top; margin-bottom: 5px;"><strong>Report Period:</strong></td>
                <td>
                    @if(isset($dateRange))
                    @php
                    // Try to match 'From: ... | To: ...' and reformat
                    if (preg_match('/From:\s*([^|]+)\|\s*To:\s*([^|]+)/', $dateRange, $matches)) {
                    echo trim($matches[1]) . ' - ' . trim($matches[2]);
                    } else {
                    echo e($dateRange);
                    }
                    @endphp
                    @else
                    —
                    @endif
                </td>
            </tr>
            <tr>
                <td style="width:38%; color:#2c5aa0; vertical-align: top; margin-bottom: 5px;"><strong>Conducted By:</strong></td>
                <td style="width:62%; color:#222;">
                    @if(isset($conductedByList) && is_array($conductedByList))
                    {!! implode('<br>', array_map('e', $conductedByList)) !!}
                    @else
                    {{ $conductedBy ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="width:38%; color:#2c5aa0; vertical-align: top; margin-bottom: 5px;"><strong>Students:</strong></td>
                <td>{{ count($consultations) }}</td>
            </tr>
            <tr>
                <td style="width:38%; color:#2c5aa0; vertical-align: top; margin-bottom: 5px;"><strong>Consultations:</strong></td>
                <td>{{ collect($consultations)->sum(function($group) { return count($group['consultations']); }) }}</td>
            </tr>
        </table>
    </div>
    @else
    <div class="no-consultations">
        <h3>No Consultation Records Found</h3>
        <p>No consultations match the specified criteria.</p>
    </div>
    @endif

    <!-- Computer Generated Message -->
    <div style="text-align: center; margin-top: 40px; margin-bottom: 20px; border-top: 2px solid #207cca; padding-top: 15px;">
        <p style="margin: 0; font-size: 11px; color: #1e5799; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">This is Computer Generated</p>
        <p style="margin: 5px 0 0 0; font-size: 9px; color: #888; font-style: italic;">Clinic Demo - Medical Service Unit Demo</p>
    </div>

</body>

</html>