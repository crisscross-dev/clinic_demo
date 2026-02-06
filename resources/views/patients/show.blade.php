@extends('layouts.app')
@section('title', 'Patient Information')

@section('content')
@push('styles')
@vite(['resources/css/patients/show.css'])
@endpush

<div class="main-content">
    <div class="header-actions d-flex align-items-center">
        <!-- Desktop / wide screens: show individual buttons -->
        <div class="d-none d-md-inline-block">
            <form action="{{ route('patients.uploads', $patient->id) }}" method="GET" class="d-inline me-2">
                <button type="submit" class="btn-general btn-blue">Uploads</button>
            </form>
            <button type="button" class="btn-general btn-blue me-2" data-bs-toggle="modal" data-bs-target="#gridExampleModal">
                View Consultation
            </button>
            <button type="button" class="btn-general btn-blue me-2" data-bs-toggle="modal" data-bs-target="#createConsultationModal">
                Start Consultation
            </button>
            <a href="{{ route('patients.downloadSnappy', data_get($patient,'id')) }}" class="btn-general btn-gray me-2" role="button" title="Download Patient Information">
                <i class="bi bi-download"></i>
            </a>
            <form action="{{ route('patients.index') }}" method="GET" class="d-inline">
                <button type="submit" class="btn-general btn-gray">back</button>
            </form>
        </div>

        <!-- Small screens: single dropdown with all actions -->
        <div class="d-inline-block d-md-none ms-1 align-self-end">
            <div class="dropdown">
                <button class="btn-general btn-blue dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionsDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('patients.uploads', $patient->id) }}">Uploads</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#gridExampleModal">view consultation</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createConsultationModal">Start Consultation</a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('patients.downloadSnappy', data_get($patient,'id')) }}">Download</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('patients.index') }}">Back</a>
                    </li>
                </ul>
            </div>
        </div>



    </div>

    <div class="information-details">
        <div class="information-details-header">
            <h3>Personal Info</h3>
        </div>
        <ul class="details-list">
            <li>
                <span class="highlighted-value text-uppercase">
                    <strong>Name:</strong>
                    {{ $patientData['last_name'] }}, {{ $patientData['first_name'] }}
                </span>
            </li>
            <li><strong>Middlename:</strong> {{ $patientData['middle_name'] }}</li>
            <li><strong>Suffix:</strong> {{ $patientData['suffix'] }}</li>
            <li><strong>Age:</strong> {{ $patientData['age'] }}</li>
            <li><strong>Department:</strong> {{ $patientData['department'] }}</li>
            <li><strong>Course:</strong> {{ $patientData['course'] }}</li>
            <li><strong>Year Level:</strong> {{ $patientData['year_level'] }}</li>
            <li><strong>Sex:</strong> {{ $patientData['sex'] }}</li>
            <li><strong>Birthdate:</strong> {{ $formatted['birthdateStr'] }}</li>
            <li><strong>Nationality:</strong>{{ $patientData['nationality'] }}</li>
            <li><strong>Contact:</strong> {{ $patientData['contact_no'] }}</li>
            <li><strong>Address:</strong>{{ $patientData['address'] }}</li>
    </div>

    <div class="information-details">
        <div class="information-details-header">
            <h3>Family</h3>
        </div>
        <ul class="details-list">
            <li><strong>Father:</strong> {{ $patientData['father_name'] }}</li>
            <li><strong>Father Contact:</strong> {{ $patientData['father_contact_no'] }}</li>
            <li><strong>Mother:</strong> {{ $patientData['mother_name'] }}</li>
            <li><strong>Mother Contact:</strong> {{ $patientData['mother_contact_no'] }}</li>
            <li><strong>Guardian:</strong> {{ $patientData['guardian_name'] }}</li>
            <li><strong>Guardian Relation:</strong> {{ $patientData['guardian_relationship'] }}</li>
            <li><strong>Guardian Contact:</strong> {{ $patientData['guardian_contact_no'] }}</li>
            <li><strong>Guardian Address:</strong> {{ $patientData['guardian_address'] }}</li>

        </ul>
    </div>

    <div class="information-details">
        <div class="information-details-header">
            <h3>Medical</h3>
        </div>
        <ul class="details-list">
            <li><strong>Allergies:</strong><br>{!! $formatted['allergies'] !!}</li>
            <li><strong>Other Allergies:</strong><br> {{ $patientData['other_allergies'] }}</li>
            <li><strong>Treatment:</strong><br>{!! $formatted['treatments'] !!}</li>
            <li><strong>COVID Status:</strong><br>{!! $formatted['covid'] !!}</li>
            <li><strong>Flu Vaccine:</strong><br> {{ $patientData['flu_vaccine'] }}</li>
            <li><strong>Other Vaccine:</strong><br> {{ $patientData['other_vaccine'] }}</li>
            <li><strong>Medical History:</strong><br> {{ $patientData['medical_history'] }}</li>
            <li><strong>Maintenance Medication:</strong><br> {{ $patientData['medication'] }}</li>
            <li><strong>Last Hospitalization:</strong><br> {{ $patientData['lasthospitalization'] }}</li>
        </ul>
    </div>

    <div class="information-details">
        <div class="information-details-header">
            <h3>Consent</h3>
        </div>
        <ul class="details-list">
            <li><strong>Consent:</strong><br>{!! $formatted['consent'] !!}</li>
            <li><strong>Consent by:</strong><br>{{ $patientData['consent_by'] }}</li>
            <li><strong>Date Submitted:</strong><br> {{ $formatted['createdStr'] }}</li>
            <li><strong>Signature:</strong><br>
                @if($patient->signature)
                @php
                $sigPath = storage_path('app/private/' . ltrim($patient->signature, '/'));
                @endphp
                @if(file_exists($sigPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}"
                    alt="Signature"
                    style="max-width: 250px; max-height: 100px; border:1px solid #ccc; border-radius:6px; cursor:pointer;"
                    data-bs-toggle="modal" data-bs-target="#signatureModal">
                @else
                <span class="text-muted">Signature file not found</span>
                @endif
                @else
                <span class="text-muted">—</span>
                @endif
            </li>
            <!-- Modal -->
            <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title" id="signatureModalLabel">Consent Signature</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            @if($patient->signature)
                            @php
                            $sigPath = storage_path('app/private/' . ltrim($patient->signature, '/'));
                            @endphp
                            @if(file_exists($sigPath))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}"
                                alt="Signature Full"
                                style="max-width: 100%; height: auto; border:1px solid #ddd; border-radius:8px;">
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </ul>
    </div>
</div> 
@includeWhen(isset($consultations), 'patients.consultations.index', ['patient' => $patient, 'consultations' => $consultations])
@include('patients.consultations.edit', ['patient' => $patient])
@include('patients.consultations.create', ['patient' => $patient])


@push('scripts')
@vite(['resources/js/patients/show.js',
])
@endpush

@endsection