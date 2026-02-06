@extends('layouts.student')

@section('title', 'Student Dashboard - Samuel Clinic')

@push('styles')
@vite([
'resources/css/index.css',
'resources/css/partials/health-form.css',
'resources/css/patients/show.css',
'resources/css/student/dashboard.css',
])

@endpush

@section('content')
<div class="main-content" data-show-privacy-policy="{{ !($hasSeenPrivacyPolicy ?? true) ? '1' : '0' }}"
    data-profile-completion="{{ $patientInfo && isset($patientInfo->completion_status) ? $patientInfo->completion_status['percentage'] : 0 }}">

    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        @include('partials.flash-messages')

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-card welcome-card p-4" id="welcomeCard">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Welcome back Demonstrator!</h2>
                            <p class="mb-0 opacity-75">
                                @if($patientInfo ?? false)
                                {{ $patientInfo->full_name }}
                                @else
                                {{ $student->email ?? 'Student' }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($patientInfo ?? false)
                            @php
                            $status = $patientInfo->completion_status;
                            @endphp
                            <div class="profile-status-highlight clickable" role="button" onclick="openHealthFormModal()">
                                <div class="status-label">Profile Status</div>
                                <div class="status-content">
                                    <i class="bi bi-person-check me-2"></i>
                                    <span class="badge bg-{{ $status['color'] }} fs-6 px-3 py-2 me-2">{{ $status['percentage'] }}%</span>
                                    <span class="status-text">{{ $status['text'] }}</span>
                                </div>
                                <small class="click-hint">Click to update profile</small>
                            </div>
                            @else
                            <div class="profile-status-highlight incomplete clickable" role="button" onclick="openHealthFormModal()">
                                <div class="status-label">Profile Status</div>
                                <div class="status-content">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 me-2">0%</span>
                                    <span class="status-text">Incomplete</span>
                                </div>
                                <small class="click-hint">Click to complete profile</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-4 stats-row">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card p-3 clickable" role="button" tabindex="0" onclick="openProfileModal()">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary me-3">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Profile</h6>
                            <h4 class="mb-0 text-primary" style="font-size:1.1rem; font-weight:500;">View</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary me-3">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Consultations</h6>
                            <h4 class="mb-0">{{ $consultations->count() ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning me-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Last Visit</h6>
                            <h4 class="mb-0">
                                @if(($consultations ?? collect())->isNotEmpty())
                                {{ $consultations->first()->created_at->diffForHumans() }}
                                @else
                                <small>No visits</small>
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info me-3">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Consent Status</h6>
                            @php
                            // Determine the latest consent request status (if any)
                            $latestConsent = null;
                            if ($patientInfo && $patientInfo->consentRequests) {
                            $latestConsent = $patientInfo->consentRequests->sortByDesc('created_at')->first();
                            }
                            $latestStatus = $latestConsent->status ?? null;
                            @endphp

                            @if(($patientInfo->consent_access_requested ?? false) === true)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split text-warning me-2"></i>
                                <span class="text-warning fw-semibold">Request Pending</span>
                            </div>
                            @elseif(($patientInfo->consent_form ?? false) === true)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-lock-fill text-danger me-2"></i>
                                <span class="text-danger fw-semibold">Locked</span>
                            </div>
                            @elseif($latestStatus === 'granted')
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span class="text-success fw-semibold">Access Granted</span>
                            </div>
                            @elseif($latestStatus === 'declined')
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill text-danger me-2"></i>
                                <span class="text-danger fw-semibold">Access Denied</span>
                            </div>
                            @else
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span class="text-success fw-semibold">Unlocked</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Recent Consultations -->
            <div class="col-lg-12 mb-4">
                <div class="dashboard-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Consultations</h5>
                    </div>

                    @if(($consultations ?? collect())->isNotEmpty())
                    <div class="consultation-list">
                        @foreach($consultations as $consultation)
                        <div class="consultation-item"
                            role="button"
                            data-bs-toggle="modal"
                            data-bs-target="#consultationDetailModal"
                            data-consultation-id="{{ $consultation->id }}"
                            data-chief-complaint="{{ $consultation->chief_complaint ?? 'General Consultation' }}"
                            data-date="{{ $consultation->created_at->format('F j, Y \\a\\t g:i A') }}"
                            data-temperature="{{ $consultation->temperature ?? '—' }}"
                            data-blood-pressure="{{ $consultation->blood_pressure ?? '—' }}"
                            data-pulse-rate="{{ $consultation->pulse_rate ?? '—' }}"
                            data-respiratory-rate="{{ $consultation->respiratory_rate ?? '—' }}"
                            data-spo2="{{ $consultation->spo2 ?? '—' }}"
                            data-pain-scale="{{ $consultation->pain_scale ?? '—' }}"
                            data-assessment="{{ $consultation->assessment ?? 'No assessment available' }}"
                            data-intervention="{{ $consultation->intervention ?? 'No intervention recorded' }}"
                            data-outcome="{{ $consultation->outcome ?? 'No outcome recorded' }}"
                            data-assessed-by="{{ $consultation->assessed_by ?? 'Unknown' }}"
                            data-medicines='@json($consultation->dispensed_medicines ?? [])'>

                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="consultation-date-badge">
                                            {{ $consultation->created_at->format('M d') }}
                                        </span>
                                        <span class="consultation-status">
                                            <i class="bi bi-check-circle"></i>
                                            Completed
                                        </span>
                                    </div>
                                    <h6 class="consultation-title">
                                        <i class="bi bi-clipboard-pulse me-2 text-primary"></i>
                                        {{ $consultation->chief_complaint ?? 'General Consultation' }}
                                    </h6>
                                    <p class="consultation-preview">
                                        {{ Str::limit($consultation->assessment ?? 'No assessment available', 85) }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="consultation-meta mb-1">
                                        {{ $consultation->created_at->format('g:i A') }}
                                    </div>
                                    <div class="consultation-action mb-2">
                                        <i class="bi bi-eye"></i> View Details
                                    </div>
                                    <div>
                                        <a href="{{ route('student.consultations.download', $consultation) }}" class="btn btn-sm btn-outline-primary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                            <span class="d-none d-md-inline">Download</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x display-6 mb-3"></i>
                        <h6>No consultations yet</h6>
                        <p class="mb-0 small">Your consultation history will appear here</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title" id="privacyPolicyModalLabel">
                    <i class="bi bi-shield-lock me-2"></i>
                    Privacy Policy & Terms of Service
                </h5>
            </div>
            <div class="modal-body" style="max-height: 60vh;">
                <p class="text-muted mb-4">Please review our privacy policy and terms before continuing.</p>

                <div class="privacy-content">
                    <h6 class="fw-bold mb-3">Information We Collect</h6>
                    <ul class="mb-4">
                        <li>Personal details (name, email, contact, address)</li>
                        <li>Student info (department, course, year level)</li>
                        <li>Medical history and health records</li>
                        <li>Consultation records and vital signs</li>
                        <li>Guardian contact information</li>
                    </ul>

                    <h6 class="fw-bold mb-3">How We Use Your Information</h6>
                    <ul class="mb-4">
                        <li>Provide healthcare services</li>
                        <li>Maintain medical records</li>
                        <li>Contact you about health and appointments</li>
                        <li>Improve clinic services</li>
                        <li>Meet legal requirements</li>
                    </ul>

                    <h6 class="fw-bold mb-3">Data Sharing</h6>
                    <p class="mb-2">Your information is only shared with:</p>
                    <ul class="mb-4">
                        <li>Clinic staff for treatment</li>
                        <li>Your guardian (with consent)</li>
                        <li>Authorities when legally required</li>
                    </ul>

                    <h6 class="fw-bold mb-3">Your Rights</h6>
                    <ul class="mb-4">
                        <li>Access and review your records</li>
                        <li>Request corrections</li>
                        <li>Manage consent settings</li>
                        <li>Download your information</li>
                    </ul>

                    <h6 class="fw-bold mb-3">Consent for Minors</h6>
                    <p class="mb-4">If under 18, some medical information may require parental consent.</p>

                    <h6 class="fw-bold mb-3">Your Responsibilities</h6>
                    <ul class="mb-4">
                        <li>Provide accurate information</li>
                        <li>Keep login credentials secure</li>
                        <li>Don't share your account</li>
                        <li>Follow medical advice</li>
                        <li>Report unauthorized access</li>
                    </ul>

                    <div class="alert alert-warning mb-0">
                        <strong>By accepting,</strong> you agree to these terms and confirm you've read this policy.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-general btn-gray" id="privacyDeclineBtn">
                    <i class="bi bi-x-circle me-2"></i>I Decline
                </button>
                <button type="button" class="btn-general btn-blue" id="privacyAcceptBtn">
                    <i class="bi bi-check-circle me-2"></i>I Accept
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Profile Information Modal -->
<div class="modal fade" id="profileInfoModal" tabindex="-1" aria-labelledby="profileInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title" id="profileInfoModalLabel">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    My Profile Information

                    @if($patientInfo && !empty($patientInfo->department))
                    @if($patientInfo->status === 'pending')
                    <span class="badge bg-warning text-dark ms-2">
                        Waiting for approval <i class="bi bi-hourglass-split"></i>
                    </span>
                    @elseif($patientInfo->status === 'approved')
                    <span class="badge bg-success ms-2">
                        Approved <i class="bi bi-check-circle-fill"></i>
                    </span>
                    @endif
                    @endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($patientInfo ?? false)
                <div class="information-details">
                    <div class="information-details-header">
                        <h3>Personal Info</h3>
                    </div>
                    <ul class="details-list">
                        <li>
                            <span class="highlighted-value text-uppercase">
                                <strong>Name:</strong>
                                {{ $patientInfo->last_name }}, {{ $patientInfo->first_name }}
                            </span>
                        </li>
                        <li><strong>Middlename:</strong> {{ $patientInfo->middle_name ?? '—' }}</li>
                        <li><strong>Suffix:</strong> {{ $patientInfo->suffix ?? '—' }}</li>
                        <li><strong>Age:</strong> {{ $patientInfo->age ?? '—' }}</li>
                        <li><strong>Department:</strong> {{ $patientInfo->department ?? '—' }}</li>
                        <li><strong>Course:</strong> {{ $patientInfo->course ?? '—' }}</li>
                        <li><strong>Year Level:</strong> {{ $patientInfo->year_level ?? '—' }}</li>
                        <li><strong>Sex:</strong> {{ $patientInfo->sex ?? '—' }}</li>
                        <li><strong>Birthdate:</strong> {{ $patientInfo->birthdate ? $patientInfo->birthdate->format('F j, Y') : '—' }}</li>
                        <li><strong>Nationality:</strong> {{ $patientInfo->nationality ?? '—' }}</li>
                        <li><strong>Contact:</strong> {{ $patientInfo->contact_no ?? '—' }}</li>
                        <li><strong>Address:</strong> {{ $patientInfo->address ?? '—' }}</li>
                    </ul>
                </div>

                <div class="information-details">
                    <div class="information-details-header">
                        <h3>Family</h3>
                    </div>
                    <ul class="details-list">
                        <li><strong>Father:</strong> {{ $patientInfo->father_name ?? '—' }}</li>
                        <li><strong>Father Contact:</strong> {{ $patientInfo->father_contact_no ?? '—' }}</li>
                        <li><strong>Mother:</strong> {{ $patientInfo->mother_name ?? '—' }}</li>
                        <li><strong>Mother Contact:</strong> {{ $patientInfo->mother_contact_no ?? '—' }}</li>
                        <li><strong>Guardian:</strong> {{ $patientInfo->guardian_name ?? '—' }}</li>
                        <li><strong>Guardian Relation:</strong> {{ $patientInfo->guardian_relationship ?? '—' }}</li>
                        <li><strong>Guardian Contact:</strong> {{ $patientInfo->guardian_contact_no ?? '—' }}</li>
                        <li><strong>Guardian Address:</strong> {{ $patientInfo->guardian_address ?? '—' }}</li>
                    </ul>
                </div>

                <div class="information-details">
                    <div class="information-details-header">
                        <h3>Medical</h3>
                    </div>
                    <ul class="details-list">
                        <li><strong>Allergies:</strong><br>{{ $patientInfo->allergies ?? '—' }}</li>
                        <li><strong>Other Allergies:</strong><br>{{ $patientInfo->other_allergies ?? '—' }}</li>
                        <li><strong>Treatment:</strong><br>{{ $patientInfo->treatments ?? '—' }}</li>
                        <li><strong>COVID Status:</strong><br>{{ $patientInfo->covid ?? '—' }}</li>
                        <li><strong>Flu Vaccine:</strong><br>{{ $patientInfo->flu_vaccine ?? '—' }}</li>
                        <li><strong>Other Vaccine:</strong><br>{{ $patientInfo->other_vaccine ?? '—' }}</li>
                        <li><strong>Medical History:</strong><br>{{ $patientInfo->medical_history ?? '—' }}</li>
                        <li><strong>Maintenance Medication:</strong><br>{{ $patientInfo->medication ?? '—' }}</li>
                        <li><strong>Last Hospitalization:</strong><br>{{ $patientInfo->lasthospitalization ?? '—' }}</li>
                    </ul>
                </div>

                <div class="information-details">
                    <div class="information-details-header">
                        <h3>Consent</h3>
                    </div>
                    <ul class="details-list">
                        <li><strong>Consent:</strong><br>
                            @if($patientInfo->consent ?? false)
                            @php
                            $consentItems = is_string($patientInfo->consent) ? explode(',', $patientInfo->consent) : (is_array($patientInfo->consent) ? $patientInfo->consent : []);
                            $consentItems = array_map('trim', $consentItems);
                            @endphp
                            @if(!empty($consentItems))
                            <ul class="ms-3 mt-2">
                                @foreach($consentItems as $consentItem)
                                <li class="mb-1">{{ $consentItem }}</li>
                                @endforeach
                            </ul>
                            @else
                            —
                            @endif
                            @else
                            —
                            @endif
                        </li>
                        <li><strong>Consent by:</strong><br>{{ $patientInfo->consent_by ?? '—' }}</li>
                        <li><strong>Date Submitted:</strong><br>{{ $patientInfo->created_at ? $patientInfo->created_at->format('F j, Y \a\t g:i A') : '—' }}</li>
                        <li><strong>Signature:</strong><br>
                            @php
                            // Use the student-specific signature route with cache-busting timestamp
                            $signaturePath = data_get($patientInfo, 'signature');
                            $hasSignature = !empty($signaturePath);
                            $ts = $patientInfo->updated_at?->timestamp ?? now()->timestamp;
                            $signatureUrl = $hasSignature ? route('student.signature', ['patient' => $patientInfo->id]) . '?t=' . $ts : null;
                            @endphp

                            @if($hasSignature && $signatureUrl)
                            <img id="profile-signature-img" data-base-src="{{ route('student.signature', ['patient' => $patientInfo->id]) }}" src="{{ $signatureUrl }}"
                                alt="Signature"
                                style="max-width: 350px; max-height: 150px; border:1px solid #ccc; border-radius:6px;"
                                onload="console.log('Signature loaded successfully from student route')"
                                onerror="console.log('Signature failed to load from student route'); this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <small style="display:none; color:#dc3545; font-style:italic;">❌ Signature not accessible</small>
                            @else
                            <span class="text-muted">No signature available</span>
                            @endif
                        </li>
                    </ul>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Profile Information</h4>
                    <p class="text-muted">You haven't completed your profile yet. Click the button below to get started.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#healthFormModal">
                        <i class="bi bi-person-plus me-2"></i>Complete Profile
                    </button>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @if($patientInfo ?? false)
                <div class="modal-footer d-flex justify-content-end flex-wrap">
                    <button type="button" class="btn-general btn-gray me-2" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="button" class="btn-general btn-blue me-2"
                        data-pdf-url="{{ route('student.profile.pdf', ['patient' => $patientInfo->id]) }}"
                        onclick="window.open(this.getAttribute('data-pdf-url'), '_blank')">
                        <i class="bi bi-download me-2"></i>
                        <span class="d-none d-sm-inline">Download PDF</span>
                        <span class="d-inline d-sm-none">PDF</span>
                    </button>

                    <button type="button" class="btn-general btn-blue"
                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#healthFormModal">
                        <i class="bi bi-pencil me-2"></i>
                        <span class="d-none d-sm-inline">Edit Information</span>
                        <span class="d-inline d-sm-none">Edit</span>
                    </button>
                </div>

                @else
                <button type="button" class="btn-general btn-secondary" data-bs-dismiss="modal">Close</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Consultation Detail Modal -->
<div class="modal fade" id="consultationDetailModal" tabindex="-1" aria-labelledby="consultationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="consultationDetailModalLabel">
                    <i class="bi bi-clipboard-pulse me-2"></i>
                    Consultation Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>Chief Complaint:</strong>
                                        <p class="mb-0" id="modal-chief-complaint">—</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Date & Time:</strong>
                                        <p class="mb-0" id="modal-date">—</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Assessed By:</strong>
                                        <p class="mb-0" id="modal-assessed-by">—</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vital Signs -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-success mb-3">
                                    <i class="bi bi-heart-pulse me-2"></i>Vital Signs
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-thermometer text-danger me-2"></i>
                                            <div>
                                                <small class="text-muted">Temperature</small>
                                                <p class="mb-0 fw-semibold" id="modal-temperature">—</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-activity text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted">Blood Pressure</small>
                                                <p class="mb-0 fw-semibold" id="modal-blood-pressure">—</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-heart text-danger me-2"></i>
                                            <div>
                                                <small class="text-muted">Pulse Rate</small>
                                                <p class="mb-0 fw-semibold" id="modal-pulse-rate">—</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-lungs text-info me-2"></i>
                                            <div>
                                                <small class="text-muted">Respiratory Rate</small>
                                                <p class="mb-0 fw-semibold" id="modal-respiratory-rate">—</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-moisture text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted">SpO2</small>
                                                <p class="mb-0 fw-semibold" id="modal-spo2">—</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted">Pain Scale</small>
                                                <p class="mb-0 fw-semibold" id="modal-pain-scale">—</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Details -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-warning mb-3">
                                    <i class="bi bi-clipboard-check me-2"></i>Clinical Details
                                </h6>
                                <div class="mb-3">
                                    <strong>Assessment:</strong>
                                    <p class="mb-0 mt-1" id="modal-assessment">—</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Intervention:</strong>
                                    <p class="mb-0 mt-1" id="modal-intervention">—</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Medicine Given:</strong>
                                    <ul class="mb-0 ps-3" id="modal-medicines">
                                        <li class="text-muted">—</li>
                                    </ul>
                                </div>
                                <div class="mb-0">
                                    <strong>Outcome:</strong>
                                    <p class="mb-0 mt-1" id="modal-outcome">—</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Health Information Form Modal -->
<div class="modal fade" id="healthFormModal" tabindex="-1" aria-labelledby="healthFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title" id="healthFormModalLabel">
                    <i class="bi bi-file-medical me-2"></i>
                    Health Information

                    @if($patientInfo && !empty($patientInfo->department))
                    @if($patientInfo->status === 'pending')
                    <span class="badge bg-warning text-dark ms-2">
                        Waiting for approval <i class="bi bi-hourglass-split"></i>
                    </span>
                    @elseif($patientInfo->status === 'approved')
                    <span class="badge bg-success ms-2">
                        Approved <i class="bi bi-check-circle-fill"></i>
                    </span>
                    @endif
                    @endif

                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('partials.health-form', ['patientInfo' => $patientInfo ?? null])
            </div>
        </div>
    </div>
</div>

<!-- Request Consent Access Modal -->
<div class="modal fade" id="requestConsentAccessModal" tabindex="-1" aria-labelledby="requestConsentAccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title" id="requestConsentAccessModalLabel">
                    <i class="bi bi-unlock me-2"></i>Request Consent Access
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Why do you need access?</strong>
                    <p class="mb-0 mt-2 small">Please provide a valid reason for requesting access to edit your consent information. This will be reviewed by the administrator.</p>
                </div>
                <form id="consentAccessRequestForm">
                    @csrf
                    <div class="mb-3">
                        <label for="request_reason" class="form-label">Reason for Request *</label>
                        <textarea
                            id="request_reason"
                            name="request_reason"
                            class="form-control"
                            rows="4"
                            placeholder="Example: I need to update my consent information because my parent/guardian contact has changed..."
                            required
                            minlength="20"
                            maxlength="500"></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/500 characters (minimum 20 characters)
                        </div>
                        <div class="invalid-feedback">
                            Please provide a detailed reason (at least 20 characters).
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <button type="button" class="btn-general btn-blue" id="submitConsentAccessRequest">
                    <i class="bi bi-send me-2"></i>Submit Request
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
@vite([
'resources/js/index.js',
'resources/js/shared/checkbox.js',
'resources/js/partials/health-form.js',
])

<!-- Profile Completion Overlay -->
<div class="profile-completion-overlay" id="profileCompletionOverlay"></div>

<script>
    function openHealthFormModal() {
        const modal = new bootstrap.Modal(document.getElementById('healthFormModal'));
        modal.show();
    }

    function openProfileModal() {
        const modal = new bootstrap.Modal(document.getElementById('profileInfoModal'));
        modal.show();
    }


    document.addEventListener('DOMContentLoaded', function() {
        // Check profile completion and show overlay if below 25%
        const mainContent = document.querySelector('.main-content');
        const profileCompletion = parseInt(mainContent?.getAttribute('data-profile-completion') || '0');
        const shouldShowPrivacyPolicy = mainContent && mainContent.getAttribute('data-show-privacy-policy') === '1';

        // Show overlay and highlight profile status if completion is below 25% and privacy policy has been seen
        if (profileCompletion < 25 && !shouldShowPrivacyPolicy) {
            const overlay = document.getElementById('profileCompletionOverlay');
            const profileStatusHighlight = document.querySelector('.profile-status-highlight');
            const welcomeCard = document.getElementById('welcomeCard');

            if (overlay && profileStatusHighlight) {
                // Show overlay
                overlay.classList.add('active');

                // Dim the welcome card
                if (welcomeCard) {
                    welcomeCard.classList.add('dimmed');
                }

                // Highlight profile status
                profileStatusHighlight.classList.add('force-highlight');

                // Remove overlay when profile status is clicked
                profileStatusHighlight.addEventListener('click', function() {
                    overlay.classList.remove('active');
                    profileStatusHighlight.classList.remove('force-highlight');
                    if (welcomeCard) {
                        welcomeCard.classList.remove('dimmed');
                    }
                });

                // Optional: Allow clicking overlay to dismiss (with message)
                overlay.addEventListener('click', function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Complete Your Profile',
                        text: 'Please complete your profile to access the full dashboard. Click the highlighted profile status button to continue.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#1e5799'
                    });
                });
            }
        }

        // Privacy Policy Modal - Show on first login

        if (shouldShowPrivacyPolicy) {
            const privacyModal = new bootstrap.Modal(document.getElementById('privacyPolicyModal'), {
                backdrop: 'static',
                keyboard: false
            });
            privacyModal.show();

            // Handle Accept button
            document.getElementById('privacyAcceptBtn').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;

                // Disable button and show loading
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-spinner spinner-border spinner-border-sm me-2"></i>Processing...';

                // Send AJAX request to mark as accepted
                fetch('{{ route("student.privacy.accept") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal and show welcome message
                            privacyModal.hide();

                            Swal.fire({
                                icon: 'success',
                                title: 'Welcome!',
                                text: 'Thank you for accepting our Privacy Policy and Terms of Service.',
                                confirmButtonText: 'Get Started',
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            throw new Error(data.message || 'Failed to accept privacy policy');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to process your acceptance. Please try again.',
                            confirmButtonText: 'OK'
                        });

                        // Re-enable button
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            });

            // Handle Decline button
            document.getElementById('privacyDeclineBtn').addEventListener('click', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Privacy Policy Required',
                    html: `
            <p>You must accept our Privacy Policy and Terms of Service to continue using HWSC System.</p>
            <p class="mt-2"><strong>If you decline, you will be logged out.</strong></p>
        `,
                    showCancelButton: true,
                    confirmButtonText: 'Review Again',
                    cancelButtonText: 'Logout Now',
                    customClass: {
                        confirmButton: 'btn-general btn-blue',
                        cancelButton: 'btn-general btn-red',
                        actions: 'gap-3'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isDismissed || result.dismiss === Swal.DismissReason.cancel) {
                        // Create a form to submit POST request for logout
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("student.logout") }}';

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        document.body.appendChild(form);
                        form.submit();
                    }
                    // If they choose to review again, modal stays open
                });
            });

        }

        // Set progress bar widths from data attributes
        document.querySelectorAll('.progress-bar[data-width]').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            bar.style.width = width + '%';
        });

        // Character counter for consent access request reason
        const requestReason = document.getElementById('request_reason');
        const charCount = document.getElementById('charCount');
        if (requestReason && charCount) {
            requestReason.addEventListener('input', function() {
                charCount.textContent = this.value.length;
                // Update validation state
                if (this.value.length >= 20) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (this.value.length > 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                }
            });
        }

        // Handle consent access request submission
        const submitConsentAccessBtn = document.getElementById('submitConsentAccessRequest');
        if (submitConsentAccessBtn) {
            submitConsentAccessBtn.addEventListener('click', function() {
                const form = document.getElementById('consentAccessRequestForm');
                const reasonTextarea = document.getElementById('request_reason');
                const reason = reasonTextarea.value.trim();

                // Validate reason
                if (reason.length < 20) {
                    reasonTextarea.classList.add('is-invalid');
                    reasonTextarea.focus();
                    return;
                }

                const btn = this;
                const originalText = btn.innerHTML;

                // Disable button and show loading
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-spinner spinner-border spinner-border-sm me-2"></i>Submitting...';

                // Send AJAX request
                fetch('{{ route("patient.requestConsentAccess") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('requestConsentAccessModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Request Submitted!',
                                text: data.message || 'Your consent access request has been submitted successfully. Please wait for admin approval.',
                                confirmButtonText: 'OK',
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                // Reload page to show updated status
                                window.location.reload();
                            });
                        } else {
                            // Show error or already requested message
                            Swal.fire({
                                icon: data.already_requested ? 'info' : 'error',
                                title: data.already_requested ? 'Already Requested' : 'Error',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });

                            // Re-enable button if not already requested
                            if (!data.already_requested) {
                                btn.disabled = false;
                                btn.innerHTML = originalText;
                            } else {
                                // Close modal and reload
                                const modal = bootstrap.Modal.getInstance(document.getElementById('requestConsentAccessModal'));
                                if (modal) {
                                    modal.hide();
                                }
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to submit request. Please try again.',
                            confirmButtonText: 'OK'
                        });

                        // Re-enable button
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
            });
        }

        // Reset form when modal is closed
        const requestConsentAccessModal = document.getElementById('requestConsentAccessModal');
        if (requestConsentAccessModal) {
            requestConsentAccessModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('consentAccessRequestForm');
                const reasonTextarea = document.getElementById('request_reason');
                const charCountSpan = document.getElementById('charCount');

                if (form) form.reset();
                if (reasonTextarea) {
                    reasonTextarea.classList.remove('is-valid', 'is-invalid');
                }
                if (charCountSpan) charCountSpan.textContent = '0';
            });
        }

        // Handle consultation modal population
        const consultationItems = document.querySelectorAll('.consultation-item');
        consultationItems.forEach(function(item) {
            item.addEventListener('click', function() {
                // Get data attributes
                const chiefComplaint = this.getAttribute('data-chief-complaint');
                const date = this.getAttribute('data-date');
                const temperature = this.getAttribute('data-temperature');
                const bloodPressure = this.getAttribute('data-blood-pressure');
                const pulseRate = this.getAttribute('data-pulse-rate');
                const respiratoryRate = this.getAttribute('data-respiratory-rate');
                const spo2 = this.getAttribute('data-spo2');
                const painScale = this.getAttribute('data-pain-scale');
                const assessment = this.getAttribute('data-assessment');
                const intervention = this.getAttribute('data-intervention');
                const outcome = this.getAttribute('data-outcome');
                const assessedBy = this.getAttribute('data-assessed-by');

                // Populate modal fields
                document.getElementById('modal-chief-complaint').textContent = chiefComplaint;
                document.getElementById('modal-date').textContent = date;
                document.getElementById('modal-temperature').textContent = temperature + (temperature !== '—' ? '°C' : '');
                document.getElementById('modal-blood-pressure').textContent = bloodPressure + (bloodPressure !== '—' ? ' mmHg' : '');
                document.getElementById('modal-pulse-rate').textContent = pulseRate + (pulseRate !== '—' ? ' bpm' : '');
                document.getElementById('modal-respiratory-rate').textContent = respiratoryRate + (respiratoryRate !== '—' ? ' /min' : '');
                document.getElementById('modal-spo2').textContent = spo2 + (spo2 !== '—' ? '%' : '');
                document.getElementById('modal-pain-scale').textContent = painScale !== '—' ? painScale : '—';
                document.getElementById('modal-assessment').textContent = assessment;
                document.getElementById('modal-intervention').textContent = intervention;
                document.getElementById('modal-outcome').textContent = outcome;
                document.getElementById('modal-assessed-by').textContent = assessedBy;

                // Populate medicines
                const medList = document.getElementById('modal-medicines');
                medList.innerHTML = '';
                let medicines = [];
                try {
                    medicines = JSON.parse(this.getAttribute('data-medicines'));
                } catch (e) {
                    medicines = [];
                }
                if (Array.isArray(medicines) && medicines.length > 0) {
                    medicines.forEach(function(med) {
                        let li = document.createElement('li');
                        let text = med.name || '';
                        if (med.quantity) text += ' × ' + med.quantity;
                        if (med.instructions) text += '<br><small class="text-muted">' + med.instructions + '</small>';
                        li.innerHTML = text;
                        medList.appendChild(li);
                    });
                } else {
                    let li = document.createElement('li');
                    li.className = 'text-muted';
                    li.textContent = '—';
                    medList.appendChild(li);
                }
            });
        });

        // Handle form submission via AJAX to stay on dashboard
        const healthForm = document.getElementById('healthInfoForm');
        if (healthForm) {
            healthForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // CRITICAL: Ensure signature is captured before AJAX submission
                console.log('Form submission started...');

                const signaturePad = window.signaturePad; // Get the global signature pad instance
                const signatureInput = document.getElementById('signature-input');
                const canvas = document.getElementById('signature-pad');

                console.log('Signature elements check:', {
                    signaturePad: !!signaturePad,
                    signatureInput: !!signatureInput,
                    canvas: !!canvas,
                    signaturePadType: typeof signaturePad
                });

                if (signaturePad && signatureInput && canvas) {
                    const hasComposite =
                        typeof signatureInput.value === 'string' &&
                        signatureInput.value.startsWith('data:image');

                    if (hasComposite) {
                        console.log('Existing composite signature detected, re-use stored value.');
                    } else {
                        const isEmpty = signaturePad.isEmpty();
                        console.log('Signature pad state:', {
                            isEmpty
                        });

                        if (isEmpty) {
                            signatureInput.value = '';
                            console.log('Signature is empty, cleared input');
                        } else {
                            const previewCanvas = document.getElementById('signature-preview-canvas');
                            if (previewCanvas) {
                                const compositeData = previewCanvas.toDataURL('image/png');
                                signatureInput.value = compositeData;
                                console.log('Captured composite from preview canvas, length:', compositeData.length);
                            } else {
                                const signatureData = signaturePad.toDataURL('image/png');
                                signatureInput.value = signatureData;
                                console.log('Preview canvas missing, fallback to raw pad data.');
                            }
                        }
                    }
                } else {
                    console.warn('Missing signature elements:', {
                        signaturePad: !!signaturePad,
                        signatureInput: !!signatureInput,
                        canvas: !!canvas
                    });

                    // Try alternative approach - trigger the signature pad's form validation
                    const signatureForm = document.querySelector('form.health-form');
                    if (signatureForm) {
                        // Dispatch a custom event to trigger signature capture
                        const captureEvent = new CustomEvent('captureSignature');
                        signatureForm.dispatchEvent(captureEvent);
                        console.log('Dispatched captureSignature event');
                    }
                }

                // Double-check the FormData to see what's being sent
                const formData = new FormData(this);
                const signatureValue = formData.get('signature');
                console.log('Final signature in FormData:', {
                    hasSignature: !!signatureValue,
                    length: signatureValue ? signatureValue.length : 0,
                    preview: signatureValue ? signatureValue.substring(0, 50) + '...' : 'NO SIGNATURE'
                });

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            // Success - close modal and reload page to show updated profile
                            const modal = document.getElementById('healthFormModal');
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            modalInstance.hide();

                            // Reload page to show updated data and flash message
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Form submission failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Error handling - page will reload and show error via flash message
                        window.location.reload();
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });
        }
    });
</script>


@endpush