{{-- Meta tag for cache user identification --}}
@auth
<meta name="user-id" content="{{ auth()->id() }}">
@endauth

<h2 class="section-title mb-2">
    <i class="fas fa-file-medical"></i> Student Health Information Form
    @if($patientInfo ?? false)
    <span class="badge bg-info ms-2">Editing Mode</span>
    @else
    <span class="badge bg-warning ms-2">New Entry</span>
    @endif
</h2>
<p class="section-subtitle mb-3">
    @if($patientInfo ?? false)
    Update your health information below.
    @else
    Please complete the health information form.
    @endif
</p>

<form method="POST" action="{{ route('patient.submit') }}" class="needs-validation health-form" id="healthInfoForm">
    @csrf
    <!-- No need for patient_id or _method since we handle updates automatically based on student_account_id -->

    <div class="accordion" id="healthFormAccordion">
        <!-- Student Info Section -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed card-header" type="button" data-bs-toggle="collapse" data-bs-target="#studentInfoCollapse" aria-expanded="false" aria-controls="studentInfoCollapse">
                    <i class="fas fa-user-graduate me-2"></i>Student Information
                </button>
            </h2>
            <div id="studentInfoCollapse" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="department" class="form-label">Department *</label>
                            <select id="department" name="department" class="form-select" required>
                                <option value="" disabled {{ !($patientInfo->department ?? false) ? 'selected' : '' }}>Select Department</option>
                                <option value="BED - JHS" {{ ($patientInfo->department ?? '') === 'BED - JHS' ? 'selected' : '' }}>BED - JHS</option>
                                <option value="BED - SHS" {{ ($patientInfo->department ?? '') === 'BED - SHS' ? 'selected' : '' }}>BED - SHS</option>
                                <option value="HED - BSOA" {{ ($patientInfo->department ?? '') === 'HED - BSOA' ? 'selected' : '' }}>HED - BSOA</option>
                                <option value="HED - BSCPE" {{ ($patientInfo->department ?? '') === 'HED - BSCPE' ? 'selected' : '' }}>HED - BSCPE</option>
                                <option value="HED - BSP" {{ ($patientInfo->department ?? '') === 'HED - BSP' ? 'selected' : '' }}>HED - BSP</option>
                                <option value="HED - BSA/MA" {{ ($patientInfo->department ?? '') === 'HED - BSA/MA' ? 'selected' : '' }}>HED - BSA/MA</option>
                                <option value="FACULTY" {{ ($patientInfo->department ?? '') === 'FACULTY' ? 'selected' : '' }}>FACULTY</option>
                                <option value="NTS" {{ ($patientInfo->department ?? '') === 'NTS' ? 'selected' : '' }}>NTS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="course" class="form-label">Course/Section *</label>
                            <input type="text" id="course" name="course" class="form-control" value="{{ $patientInfo->course ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="year_level" class="form-label">Grade level *</label>
                            <select id="year_level" name="year_level" class="form-control" required>
                                <option value="" disabled {{ empty($patientInfo->year_level) ? 'selected' : '' }}>Select grade level</option>
                                <option value="Grade 7" {{ ($patientInfo->year_level ?? '') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                                <option value="Grade 8" {{ ($patientInfo->year_level ?? '') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                                <option value="Grade 9" {{ ($patientInfo->year_level ?? '') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                                <option value="Grade 10" {{ ($patientInfo->year_level ?? '') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                                <option value="Grade 11" {{ ($patientInfo->year_level ?? '') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                                <option value="Grade 12" {{ ($patientInfo->year_level ?? '') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                                <option value="1st year college" {{ ($patientInfo->year_level ?? '') == '1st year college' ? 'selected' : '' }}>1st year college</option>
                                <option value="2nd year college" {{ ($patientInfo->year_level ?? '') == '2nd year college' ? 'selected' : '' }}>2nd year college</option>
                                <option value="3rd year college" {{ ($patientInfo->year_level ?? '') == '3rd year college' ? 'selected' : '' }}>3rd year college</option>
                                <option value="4th year college" {{ ($patientInfo->year_level ?? '') == '4th year college' ? 'selected' : '' }}>4th year college</option>
                            </select>
                        </div>


                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" style="text-transform: capitalize;" value="{{ $patientInfo->last_name ?? '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" style="text-transform: capitalize;" value="{{ $patientInfo->first_name ?? '' }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" class="form-control" style="text-transform: capitalize;" value="{{ $patientInfo->middle_name ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="suffix" class="form-label">Suffix</label>
                            <input type="text" id="suffix" name="suffix" class="form-control" placeholder="N/A if none" style="text-transform: capitalize;" value="{{ $patientInfo->suffix ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" id="age" name="age" class="form-control" value="{{ $patientInfo->age ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select id="sex" name="sex" class="form-select">
                                <option value="" disabled {{ !($patientInfo->sex ?? false) ? 'selected' : '' }}>Select Sex</option>
                                <option value="Male" {{ ($patientInfo->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ ($patientInfo->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="birthdate" class="form-label">Date of Birth</label>
                            <input type="text" id="birthdate_display" class="form-control" placeholder="Select date" readonly style="cursor: pointer; background-color: white;">
                            <input type="hidden" id="birthdate" name="birthdate" value="{{ $patientInfo->birthdate ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" id="nationality" name="nationality" class="form-control" value="{{ $patientInfo->nationality ?? '' }}">
                        </div>

                        <div class="col-md-4">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" id="religion" name="religion" class="form-control" value="{{ $patientInfo->religion ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="contact_no" class="form-label">Contact Number</label>
                            <input type="tel" id="contact_no" name="contact_no" class="form-control" pattern="\d{11}" minlength="11" maxlength="11" inputmode="numeric" placeholder="09123456789" title="Enter exactly 11 digits" value="{{ $patientInfo->contact_no ?? '' }}">
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Complete Address</label>
                            <input type="text" id="address" name="address" class="form-control" style="text-transform: capitalize;" value="{{ $patientInfo->address ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Info Section -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed card-header" type="button" data-bs-toggle="collapse" data-bs-target="#parentGuardianCollapse" aria-expanded="false" aria-controls="parentGuardianCollapse">
                    <i class="fas fa-users me-2"></i>Parent / Guardian Information
                </button>
            </h2>
            <div id="parentGuardianCollapse" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Full Name</label>
                            <input type="text" id="father_name" name="father_name" class="form-control" style="text-transform:  capitalize;" placeholder="Surname, Firstname, M.I" value="{{ $patientInfo->father_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="father_contact_no" class="form-label">Father's Contact</label>
                            <input type="tel" id="father_contact_no" name="father_contact_no" class="form-control" pattern="\d{11}" minlength="11" maxlength="11" inputmode="numeric" placeholder="09123456789" value="{{ $patientInfo->father_contact_no ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Full Name</label>
                            <input type="text" id="mother_name" name="mother_name" class="form-control" style="text-transform: capitalize;" placeholder="Surname, Firstname, M.I" value="{{ $patientInfo->mother_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_contact_no" class="form-label">Mother's Contact</label>
                            <input type="tel" id="mother_contact_no" name="mother_contact_no" class="form-control" pattern="\d{11}" minlength="11" maxlength="11" inputmode="numeric" placeholder="09123456789" value="{{ $patientInfo->mother_contact_no ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="guardian_name" class="form-label">Guardian's Name</label>
                            <input type="text" id="guardian_name" name="guardian_name" class="form-control" style="text-transform: capitalize;" placeholder="Surname, Firstname, M.I" value="{{ $patientInfo->guardian_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_contact_no" class="form-label">Guardian's Contact</label>
                            <input type="tel" id="guardian_contact_no" name="guardian_contact_no" class="form-control" pattern="\d{11}" minlength="11" maxlength="11" inputmode="numeric" placeholder="09123456789" value="{{ $patientInfo->guardian_contact_no ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label for="guardian_relationship" class="form-label">Relation to Student</label>
                            <input type="text" id="guardian_relationship" name="guardian_relationship" class="form-control" placeholder="Father, Mother, etc." value="{{ $patientInfo->guardian_relationship ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="guardian_address" class="form-label">Guardian's Address</label>
                            <input type="text" id="guardian_address" name="guardian_address" class="form-control" style="text-transform: capitalize;" value="{{ $patientInfo->guardian_address ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information Section -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed card-header" type="button" data-bs-toggle="collapse" data-bs-target="#medicalInfoCollapse" aria-expanded="false" aria-controls="medicalInfoCollapse">
                    <i class="fas fa-heartbeat me-2"></i>Medical Information
                </button>
            </h2>
            <div id="medicalInfoCollapse" class="accordion-collapse collapse">
                <div class="accordion-body">
                    @php
                    $existingAllergies = $patientInfo->allergies ?? '';
                    $allergiesList = is_string($existingAllergies) ? explode(',', $existingAllergies) : (is_array($existingAllergies) ? $existingAllergies : []);
                    $allergiesList = array_map('trim', $allergiesList);
                    @endphp
                    <div class="mb-3">
                        <h3><label class="form-label">Allergies</label></h3>
                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_food" value="Food" {{ in_array('Food', $allergiesList) ? 'checked' : '' }}>
                                    Food (e.g., shrimp, chicken)
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_dust" value="Dust" {{ in_array('Dust', $allergiesList) ? 'checked' : '' }}>
                                    Dust / Pollen
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_heat" value="Heat" {{ in_array('Heat', $allergiesList) ? 'checked' : '' }}>
                                    Heat
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_rhinitis" value="Rhinitis" {{ in_array('Rhinitis', $allergiesList) ? 'checked' : '' }}>
                                    Allergic Rhinitis
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_drugs" value="Drugs" {{ in_array('Drugs', $allergiesList) ? 'checked' : '' }}>
                                    Drugs
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="allergies[]" id="allergy_none" value="None" {{ in_array('None', $allergiesList) ? 'checked' : '' }}>
                                    None
                                </label>
                            </div>
                        </div>
                        <textarea id="other_allergies" name="other_allergies" class="form-control mt-2" rows="2" placeholder="Other allergies (please specify)">{{ $patientInfo->other_allergies ?? '' }}</textarea>
                    </div>

                    @php
                    $existingTreatments = $patientInfo->treatments ?? '';
                    $treatmentsList = is_string($existingTreatments) ? explode(',', $existingTreatments) : (is_array($existingTreatments) ? $existingTreatments : []);
                    $treatmentsList = array_map('trim', $treatmentsList);
                    @endphp
                    <div class="mb-3">
                        <h3><label class="form-label">Treatment</label></h3>
                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="treatments[]" id="treat_antihistamine" value="Antihistamine" {{ in_array('Antihistamine', $treatmentsList) ? 'checked' : '' }}>
                                    Antihistamine (e.g., Cetirizine)
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="treatments[]" id="treat_not_specified" value="Not Specified" {{ in_array('Not Specified', $treatmentsList) ? 'checked' : '' }}>
                                    Not Specified
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="treatments[]" id="treat_none" value="None" {{ in_array('None', $treatmentsList) ? 'checked' : '' }}>
                                    None
                                </label>
                            </div>
                        </div>
                    </div>

                    @php
                    $existingCovid = $patientInfo->covid ?? '';
                    $covidList = is_string($existingCovid) ? explode(',', $existingCovid) : (is_array($existingCovid) ? $existingCovid : []);
                    $covidList = array_map('trim', $covidList);
                    @endphp
                    <div class="mb-3">
                        <h3><label class="form-label">COVID-19 Vaccination Status</label></h3>
                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="covid[]" id="covid_1st" value="1st dose" {{ in_array('1st dose', $covidList) ? 'checked' : '' }}>
                                    1st Dose
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="covid[]" id="covid_2nd" value="2nd dose" {{ in_array('2nd dose', $covidList) ? 'checked' : '' }}>
                                    2nd Dose
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="covid[]" id="covid_boost1" value="1st booster" {{ in_array('1st booster', $covidList) ? 'checked' : '' }}>
                                    1st Booster
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="covid[]" id="covid_boost2" value="2nd booster" {{ in_array('2nd booster', $covidList) ? 'checked' : '' }}>
                                    2nd Booster
                                </label>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label class="form-check">
                                    <input type="checkbox" name="covid[]" id="covid_none" value="Not vaccinated" {{ in_array('Not vaccinated', $covidList) ? 'checked' : '' }}>
                                    Not Vaccinated
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="flu_vaccine" class="form-label">Flu Vaccination (Date or Status)</label>
                            <textarea id="flu_vaccine" name="flu_vaccine" class="form-control" rows="2">{{ $patientInfo->flu_vaccine ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="other_vaccine" class="form-label">Other Vaccines (Name and Date)</label>
                            <textarea id="other_vaccine" name="other_vaccine" class="form-control" rows="2">{{ $patientInfo->other_vaccine ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3 mt-2">
                        <label for="medical_history" class="form-label">Medical History (Past or Present Conditions)</label>
                        <textarea id="medical_history" name="medical_history" class="form-control" rows="3">{{ $patientInfo->medical_history ?? '' }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="medication" class="form-label">Maintenance Medications</label>
                            <textarea id="medication" name="medication" class="form-control" rows="2">{{ $patientInfo->medication ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="lasthospitalization" class="form-label">Last Hospitalization (Date & Reason)</label>
                            <textarea id="lasthospitalization" name="lasthospitalization" class="form-control" rows="2">{{ $patientInfo->lasthospitalization ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Consent Section -->
        @php
        $existingConsent = $patientInfo->consent ?? '';
        $consentList = is_string($existingConsent) ? explode(',', $existingConsent) : (is_array($existingConsent) ? $existingConsent : []);
        $consentList = array_map('trim', $consentList);

        // Lock consent section if student has already submitted consent data
        // Consent is locked when: consent data exists AND consent_by is filled AND signature exists
        $hasConsentData = ($patientInfo ?? false) &&
        !empty($patientInfo->consent) &&
        !empty($patientInfo->consent_by) &&
        !empty($patientInfo->signature);

        // Admin can control lock via consent_form flag
        // consent_form = 0 (false) → UNLOCKED (default or admin granted access)
        // consent_form = 1 (true) → LOCKED (after submit)
        $isAdminLocked = ($patientInfo->consent_form ?? false) === true;

        // Check if admin explicitly unlocked (consent_form = 0 and has consent data)
        $adminUnlocked = ($patientInfo->consent_form ?? false) === false && $hasConsentData;

        // Final lock status: locked ONLY if consent_form = 1 (true)
        // If admin granted access (consent_form = 0), form is unlocked even if data exists
        $isConsentLocked = $isAdminLocked;
        @endphp

        <div class="accordion-item mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed card-header" type="button" data-bs-toggle="collapse" data-bs-target="#consentCollapse" aria-expanded="false" aria-controls="consentCollapse">
                    <i class="fas fa-file-signature me-2"></i>Consent
                    @if($isConsentLocked)
                    <span class="badge bg-success ms-2">Submitted & Locked</span>
                    @endif
                </button>
            </h2>
            <div id="consentCollapse" class="accordion-collapse collapse">
                <div class="accordion-body">
                    @if($isConsentLocked)
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lock fs-4 me-3"></i>
                            <div class="flex-grow-1">
                                <strong>Consent Form Locked</strong>
                                <p class="mb-2 mt-1">
                                    @if($isAdminLocked)
                                    This section is currently locked by the administrator. You cannot edit your consent information at this time.
                                    @else
                                    Your consent has been submitted and locked to maintain data integrity. To make changes, please request access from the administrator.
                                    @endif
                                </p>

                                @if($patientInfo->consent_access_requested ?? false)
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Request Pending:</strong> Your access request is awaiting admin approval.
                                    Please wait for admin approval.
                                </div>
                                @else
                                <button type="button" class="btn btn-blue btn-sm mt-2" id="openConsentAccessModal" style="pointer-events: auto; position: relative; z-index: 10;">
                                    <i class="fas fa-paper-plane me-1"></i>Request Access to Edit Consent
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="consent-checkbox {{ $isConsentLocked ? 'consent-section-disabled' : '' }}">
                        @if($isConsentLocked)
                        {{-- Hidden inputs to preserve existing consent data --}}
                        @foreach($consentList as $consent)
                        <input type="hidden" name="consent[]" value="{{ $consent }}">
                        @endforeach
                        <input type="hidden" name="consent_by" value="{{ $patientInfo->consent_by }}">
                        @if($patientInfo->signature ?? false)
                        <input type="hidden" name="signature" value="{{ $patientInfo->signature }}">
                        @endif
                        @endif

                        <label class="consent-label no-consent {{ $isConsentLocked ? 'disabled' : '' }}">
                            <input type="checkbox" name="{{ $isConsentLocked ? 'consent_display[]' : 'consent[]' }}" id="consent_none"
                                value="I do not consent any medical examination and/or treatment to be done to my child."
                                {{ in_array('I do not consent any medical examination and/or treatment to be done to my child.', $consentList) ? 'checked' : '' }}
                                {{ $isConsentLocked ? 'disabled readonly' : '' }}>
                            <span class="fw-semibold">I do not consent any medical examination and/or treatment to be done to my child.</span>
                        </label>
                        <label class="consent-label {{ $isConsentLocked ? 'disabled' : '' }}">
                            <input type="checkbox" name="{{ $isConsentLocked ? 'consent_display[]' : 'consent[]' }}" id="consent_otc"
                                value="I consent that my child be given over-the-counter medication when the need arise."
                                {{ in_array('I consent that my child be given over-the-counter medication when the need arise.', $consentList) ? 'checked' : '' }}
                                {{ $isConsentLocked ? 'disabled readonly' : '' }}>
                            I consent that my child be given over-the-counter medication when the need arise.
                        </label>
                        <label class="consent-label {{ $isConsentLocked ? 'disabled' : '' }}">
                            <input type="checkbox" name="{{ $isConsentLocked ? 'consent_display[]' : 'consent[]' }}" id="consent_firstaid"
                                value="I consent that my child be given First Aid treatment when the need arise."
                                {{ in_array('I consent that my child be given First Aid treatment when the need arise.', $consentList) ? 'checked' : '' }}
                                {{ $isConsentLocked ? 'disabled readonly' : '' }}>
                            I consent that my child be given First Aid treatment when the need arise.
                        </label>
                        <label class="consent-label {{ $isConsentLocked ? 'disabled' : '' }}">
                            <input type="checkbox" name="{{ $isConsentLocked ? 'consent_display[]' : 'consent[]' }}" id="consent_neb"
                                value="I consent the use of nebulizing kit (for asthmatic patient)."
                                {{ in_array('I consent the use of nebulizing kit (for asthmatic patient).', $consentList) ? 'checked' : '' }}
                                {{ $isConsentLocked ? 'disabled readonly' : '' }}>
                            I consent the use of nebulizing kit (for asthmatic patient).
                        </label>
                        <label class="consent-label {{ $isConsentLocked ? 'disabled' : '' }}">
                            <input type="checkbox" name="{{ $isConsentLocked ? 'consent_display[]' : 'consent[]' }}" id="consent_exam"
                                value="I consent medical/dental examination to be done to my child within the school clinic as part of routine medical examination."
                                {{ in_array('I consent medical/dental examination to be done to my child within the school clinic as part of routine medical examination.', $consentList) ? 'checked' : '' }}
                                {{ $isConsentLocked ? 'disabled readonly' : '' }}>
                            I consent medical/dental examination to be done to my child within the school clinic as part of routine medical examination.
                        </label>

                        <div class="mt-3">
                            <label for="consent_by" class="form-label">Consent by (Parent/Guardian Full Name) *</label>
                            <input type="text" id="consent_by" name="{{ $isConsentLocked ? 'consent_by_display' : 'consent_by' }}" class="form-control"
                                placeholder="Surname, First Name M.I." {{ $isConsentLocked ? '' : 'required' }} style="text-transform: capitalize;"
                                value="{{ $patientInfo->consent_by ?? '' }}"
                                {{ $isConsentLocked ? 'readonly disabled' : '' }}>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Signature *</label>
                            @if($isConsentLocked)
                            <div class="d-flex justify-content-center">
                                <div class="border rounded p-2 bg-light signature-container disabled-signature" style="width:100%; max-width:min(300px, 90vw);">
                                    @if($patientInfo->signature ?? false)
                                    @php
                                    $sig = $patientInfo->signature;
                                    // If signature is already a data URL or absolute URL, show it directly
                                    if (str_starts_with($sig, 'data:image') || str_starts_with($sig, 'http')) {
                                    $sigUrl = $sig;
                                    } else {
                                    // Use student route when viewed by a student (no admin middleware), otherwise admin route
                                    $ts = $patientInfo->updated_at?->timestamp ?? now()->timestamp;
                                    if (session('student_authenticated')) {
                                    $sigUrl = route('student.signature', $patientInfo->id) . '?t=' . $ts;
                                    } else {
                                    $sigUrl = route('patients.signature', $patientInfo->id) . '?t=' . $ts;
                                    }
                                    }
                                    @endphp
                                    <img src="{{ $sigUrl }}" alt="Existing Signature" class="img-fluid" style="border:1px solid #ccc; border-radius:6px; max-height: 260px; width: 100%;">
                                    <div class="text-center mt-2 text-muted small">
                                        <i class="fas fa-lock me-1"></i>Signature submitted and locked
                                    </div>
                                    @else
                                    <div class="text-center p-4 text-muted" style="border:1px solid #ccc; border-radius:6px; min-height: 100px; display: flex; align-items: center; justify-content: center;">
                                        <div>
                                            <i class="fas fa-signature mb-2" style="font-size: 2rem; opacity: 0.5;"></i><br>
                                            <small>No signature data available</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="signature" value="{{ $patientInfo->signature ?? '' }}">
                            @else
                            {{-- Signature Pad Display (Read-only preview) --}}
                            <div class="d-flex justify-content-center">
                                <div class="border rounded p-2 bg-light signature-container" style="width:100%; max-width:min(300px, 90vw);">
                                    <canvas id="signature-preview-canvas"
                                        style="border:1px solid #ccc; border-radius:6px; display:block; width:100%; height:180px; background: white; pointer-events: none;">
                                    </canvas>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-info-circle me-1"></i>Click draw Signature
                                        </small>
                                        <button type="button" id="openSignatureModalBtn" class="btn-general btn-blue">
                                            <i class="fas fa-pen me-1"></i>Draw Signature
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Hidden input to store the drawn signature as base64 -->
                            <input type="hidden" name="signature" id="signature-input" required data-signature-required="true" autocomplete="off">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($isConsentLocked)
        <div class="alert alert-info text-center mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <small><strong>Note:</strong> Consent section is locked to maintain data integrity. Other sections can still be updated. To modify consent information, please request access from the administrator.</small>
        </div>
        @endif

        <button type="submit" class="btn btn-blue w-100">
            <i class="fas fa-paper-plane me-2"></i>{{ ($patientInfo ?? false) ? 'Submit Health Information' : 'Submit Health Information Form' }}
        </button>
</form>

{{-- Signature Modal (Child Modal - No Backdrop) --}}
<div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signatureModalLabel">
                    <i class="fas fa-pen me-2"></i>Draw Signature
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <div class="signature-modal-container" style="width:100%; max-width:600px;">
                        <canvas id="signature-pad" width="600" height="200" style="border:2px solid #0d6efd; border-radius:6px; display:block; width:100%; height:200px; background: white; touch-action: none;">
                        </canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" id="clear-signature" class="btn btn-sm btn-secondary">
                    <i class="fas fa-redo me-1"></i>Clear
                </button>
                <div>
                    <button type="button" class="btn btn-sm btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="save-signature" class="btn btn-sm btn-primary">
                        <i class="fas fa-check me-1"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Picker Modal -->
<div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue text-white">
                <h6 class="modal-title mb-0" id="datePickerModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Select Birthdate
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="horizontal-date-picker">
                    <!-- Year Column -->
                    <div class="date-column">
                        <div class="column-header">Year</div>
                        <div class="scroll-container year-scroll">
                            @for($i = date('Y'); $i >= 1950; $i--)
                            <button type="button" class="date-btn" onclick="selectYearHorizontal('{{ $i }}', this)">{{ $i }}</button>
                            @endfor
                        </div>
                    </div>

                    <!-- Month Column -->
                    <div class="date-column">
                        <div class="column-header">Month</div>
                        <div class="scroll-container month-scroll">
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('01', 'Jan', this)">Jan</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('02', 'Feb', this)">Feb</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('03', 'Mar', this)">Mar</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('04', 'Apr', this)">Apr</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('05', 'May', this)">May</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('06', 'Jun', this)">Jun</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('07', 'Jul', this)">Jul</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('08', 'Aug', this)">Aug</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('09', 'Sep', this)">Sep</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('10', 'Oct', this)">Oct</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('11', 'Nov', this)">Nov</button>
                            <button type="button" class="date-btn" onclick="selectMonthHorizontal('12', 'Dec', this)">Dec</button>
                        </div>
                    </div>

                    <!-- Day Column -->
                    <div class="date-column">
                        <div class="column-header">Day</div>
                        <div class="scroll-container day-scroll" id="dayScrollContainer">
                            <div class="text-muted text-center p-2" style="font-size: 0.85rem;">Select year & month</div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <div class="selected-date-display" id="selectedDateDisplay">
                        <span id="displayDate">No date selected</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn-general btn-gray" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-general btn-blue" id="confirmDateBtn" onclick="confirmDate()" disabled>
                    <i class="fas fa-check me-1"></i>Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Horizontal Date Picker - Compact Design */
    #datePickerModal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    #datePickerModal .modal-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        border-bottom: none;
        padding: 0.75rem 1rem;
    }

    .horizontal-date-picker {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .date-column {
        flex: 1;
        max-width: 120px;
    }

    .column-header {
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #0d6efd;
        padding: 6px;
        background: #e7f1ff;
        border-radius: 6px 6px 0 0;
        border: 2px solid #0d6efd;
        border-bottom: none;
    }

    .scroll-container {
        height: 250px;
        overflow-y: auto;
        border: 2px solid #0d6efd;
        border-radius: 0 0 6px 6px;
        background: white;
        padding: 5px;
    }

    .date-btn {
        width: 100%;
        padding: 8px 6px;
        margin-bottom: 4px;
        border: 1.5px solid #e9ecef;
        background: white;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
        font-weight: 500;
    }

    .date-btn:hover {
        background: #e7f1ff;
        border-color: #0d6efd;
        transform: translateX(3px);
    }

    .date-btn.selected {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
    }

    .selected-date-display {
        background: #e7f1ff;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #0d6efd;
        border: 2px solid #0d6efd;
    }

    /* Scrollbar Styling */
    .scroll-container::-webkit-scrollbar {
        width: 6px;
    }

    .scroll-container::-webkit-scrollbar-track {
        background: #f1f3f5;
        border-radius: 6px;
    }

    .scroll-container::-webkit-scrollbar-thumb {
        background: #0d6efd;
        border-radius: 6px;
    }

    .scroll-container::-webkit-scrollbar-thumb:hover {
        background: #0b5ed7;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        #datePickerModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .horizontal-date-picker {
            flex-direction: row;
            gap: 6px;
            padding: 0 5px;
        }

        .date-column {
            max-width: 90px;
            min-width: 85px;
        }

        .column-header {
            font-size: 0.75rem;
            padding: 4px;
        }

        .scroll-container {
            height: 200px;
            padding: 3px;
        }

        .date-btn {
            padding: 6px 4px;
            font-size: 0.85rem;
            margin-bottom: 3px;
        }

        .selected-date-display {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 480px) {
        #datePickerModal .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100% - 0.5rem);
        }

        .horizontal-date-picker {
            gap: 4px;
        }

        .date-column {
            max-width: 80px;
            min-width: 75px;
        }

        .scroll-container {
            height: 180px;
        }
    }

    /* Clean accordion styling */
    .accordion {
        --bs-accordion-border-width: 1px;
        --bs-accordion-border-radius: 0.5rem;
    }

    .accordion-item {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .accordion-item:not(:first-child) {
        border-top: 1px solid #dee2e6;
    }

    .accordion-button {
        font-weight: 500;
        padding: 1rem 1.25rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .accordion-body {
        padding: 1.25rem;
    }

    /* Smooth transitions */
    .accordion-collapse {
        transition: height 0.35s ease;
    }

    /* Signature container responsive styles */
    .signature-container {
        min-width: 280px;
        margin: 0 auto;
        position: relative;
        /* Prevent interference with page scrolling */
        overflow: hidden;
    }

    #signature-pad {
        background-color: white;
        cursor: crosshair;
        /* Allow touch actions, but we'll handle them in JavaScript */
        touch-action: manipulation;
        /* Prevent text selection */
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        /* Prevent callout on iOS */
        -webkit-touch-callout: none;
        /* Prevent text resize on iOS */
        -webkit-text-size-adjust: none;
        /* Smooth drawing */
        image-rendering: -webkit-optimize-contrast;
        image-rendering: pixelated;
    }

    /* Mobile-specific signature improvements */
    @media (max-width: 768px) {
        .signature-container {
            /* Make signature area more prominent on mobile */
            margin: 1rem auto;
            padding: 0.75rem !important;
        }

        #signature-pad {
            /* Ensure good touch response on mobile */
            min-height: 200px;
            border-width: 2px !important;
        }

        /* Add visual hint for mobile users */
        .signature-container::before {
            content: "Touch and drag to sign";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #aaa;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 1;
            opacity: 0.7;
        }

        /* Hide hint when canvas has content */
        .signature-container.has-signature::before {
            display: none;
        }
    }

    /* Mobile optimizations */
    @media (max-width: 576px) {
        .signature-container {
            min-width: 260px;
            padding: 0.75rem !important;
        }

        .accordion-body {
            padding: 1rem;
        }

        .accordion-button {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }

    /* Extra small screens */
    @media (max-width: 375px) {
        .signature-container {
            min-width: 240px;
            padding: 0.5rem !important;
        }
    }

    /* Signature Preview Styles */
    #signature-preview-canvas {
        pointer-events: none;
        user-select: none;
    }

    .signature-container {
        position: relative;
    }

    /* Signature Modal Styles */
    #signatureModal .modal-dialog {
        max-width: 600px;
    }

    #signatureModal .signature-modal-container {
        padding: 0;
        margin: 0 auto;
    }

    #signatureModal #signature-pad {
        touch-action: none;
        cursor: crosshair;
        height: 200px;
        max-width: 100%;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        pointer-events: auto !important;
    }

    /* No backdrop overlay for signature modal */
    #signatureModal.show~.modal-backdrop {
        display: none !important;
    }

    /* Form validation styles */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    .invalid-feedback {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .valid-feedback {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #198754;
    }

    /* Required field asterisk styling */
    .form-label:has-text("*")::after {
        content: "";
        color: #dc3545;
    }

    /* Smooth transition for validation states */
    .form-control,
    .form-select {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Modern datepicker styling */
    .modern-datepicker {
        position: relative;
        cursor: pointer;
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }

    .modern-datepicker::-webkit-calendar-picker-indicator {
        cursor: pointer;
        font-size: 1.2rem;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    .modern-datepicker::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }

    .modern-datepicker:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Ensure consent request button is always clickable even in disabled section */
    #request-consent-access-btn {
        pointer-events: auto !important;
        position: relative;
        z-index: 100;
        cursor: pointer !important;
    }

    /* Disable pointer events on the disabled consent section but not its children buttons */
    .consent-section-disabled {
        pointer-events: none;
    }

    .consent-section-disabled .alert {
        pointer-events: auto;
    }

    .consent-section-disabled button {
        pointer-events: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========================================
        // FORM DATA CACHING (Auto-save & Restore)
        // Uses localStorage with 30-minute expiry
        // Works both locally and online with user-specific keys
        // ========================================

        // Create user-specific cache key to prevent conflicts
        const getUserId = function() {
            // Try to get user ID from meta tag or form data
            const metaUser = document.querySelector('meta[name="user-id"]');
            if (metaUser) return metaUser.content;

            // Fallback: generate a session-based identifier
            let sessionId = sessionStorage.getItem('form_session_id');
            if (!sessionId) {
                sessionId = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('form_session_id', sessionId);
            }
            return sessionId;
        };

        const CACHE_KEY = 'health_form_cache_' + getUserId();
        const CACHE_EXPIRY_MINUTES = 60; // Cache expires after 1 hour

        // Get form element
        const healthForm = document.getElementById('healthInfoForm');

        // Function to save form data to localStorage
        function saveFormData() {
            if (!healthForm) return;

            try {
                const formData = {};
                const formElements = healthForm.elements;

                for (let i = 0; i < formElements.length; i++) {
                    const element = formElements[i];
                    const name = element.name;
                    const type = element.type;

                    if (!name || name === '_token') continue; // Skip CSRF token

                    // Handle different input types
                    if (type === 'checkbox') {
                        if (!formData[name]) formData[name] = [];
                        if (element.checked) {
                            formData[name].push(element.value);
                        }
                    } else if (type === 'radio') {
                        if (element.checked) {
                            formData[name] = element.value;
                        }
                    } else if (type === 'select-one' || type === 'select-multiple') {
                        formData[name] = element.value;
                    } else if (type !== 'submit' && type !== 'button') {
                        formData[name] = element.value;
                    }
                }

                // Save signature data if exists
                const signatureInput = document.getElementById('signature-input');
                if (signatureInput && signatureInput.value) {
                    formData['signature'] = signatureInput.value;
                }

                // Save with timestamp and version
                const cacheData = {
                    data: formData,
                    timestamp: new Date().getTime(),
                    version: '1.0',
                    url: window.location.href
                };

                localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
                console.log('Form data cached successfully');
            } catch (error) {
                console.error('Error saving form data to cache:', error);
                // Handle quota exceeded or other localStorage errors
                if (error.name === 'QuotaExceededError') {
                    console.warn('localStorage quota exceeded. Clearing old cache...');
                    try {
                        localStorage.removeItem(CACHE_KEY);
                    } catch (e) {
                        console.error('Failed to clear cache:', e);
                    }
                }
            }
        }

        // Function to restore form data from localStorage
        function restoreFormData() {
            if (!healthForm) return;

            try {
                // Check if localStorage is available
                if (typeof(Storage) === "undefined") {
                    console.warn('localStorage is not supported in this browser');
                    return;
                }

                const cached = localStorage.getItem(CACHE_KEY);
                if (!cached) {
                    console.log('No cached form data found');
                    return;
                }

                const cacheData = JSON.parse(cached);
                const now = new Date().getTime();
                const expiryTime = CACHE_EXPIRY_MINUTES * 60 * 1000; // 30 minutes in milliseconds

                // Check if cache is expired
                if (now - cacheData.timestamp > expiryTime) {
                    console.log('Cached form data expired, clearing...');
                    localStorage.removeItem(CACHE_KEY);
                    return;
                }

                const formData = cacheData.data;
                const formElements = healthForm.elements;

                // Restore form values
                for (let i = 0; i < formElements.length; i++) {
                    const element = formElements[i];
                    const name = element.name;
                    const type = element.type;

                    if (!name || !formData.hasOwnProperty(name)) continue;

                    // Handle different input types
                    if (type === 'checkbox') {
                        const values = Array.isArray(formData[name]) ? formData[name] : [formData[name]];
                        element.checked = values.includes(element.value);
                    } else if (type === 'radio') {
                        element.checked = (element.value === formData[name]);
                    } else if (type === 'select-one' || type === 'select-multiple') {
                        element.value = formData[name];
                    } else if (type !== 'submit' && type !== 'button') {
                        element.value = formData[name] || '';
                    }
                }

                // Restore signature if exists
                if (formData['signature']) {
                    const signatureInput = document.getElementById('signature-input');
                    if (signatureInput) {
                        signatureInput.value = formData['signature'];

                        // Update signature preview
                        const previewCanvas = document.getElementById('signature-preview-canvas');
                        if (previewCanvas) {
                            const ctx = previewCanvas.getContext('2d');
                            const img = new Image();
                            img.onload = function() {
                                ctx.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
                                ctx.drawImage(img, 0, 0);
                            };
                            img.src = formData['signature'];
                        }
                    }
                }

                // Trigger change events to update any dependent fields
                toggleFacultyFields();

                console.log('Form data restored from cache successfully');
            } catch (error) {
                console.error('Error restoring form data:', error);
                // Clear corrupted cache
                try {
                    localStorage.removeItem(CACHE_KEY);
                } catch (e) {
                    console.error('Failed to clear corrupted cache:', e);
                }
            }
        }

        // Function to clear form cache
        function clearFormCache() {
            localStorage.removeItem(CACHE_KEY);
            console.log('Form cache cleared');
        }

        // Auto-save form data on input change (with debouncing)
        let saveTimeout;
        if (healthForm) {
            healthForm.addEventListener('input', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    saveFormData();
                }, 500); // Save after 500ms of no input
            });

            // Also save on change events (for selects and checkboxes)
            healthForm.addEventListener('change', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    saveFormData();
                }, 500);
            });

            // Clear cache on successful form submission
            healthForm.addEventListener('submit', function(e) {
                // Don't prevent default - let form submit normally
                // Clear cache after a short delay to ensure submission completes
                setTimeout(function() {
                    clearFormCache();
                }, 1000);
            });
        }

        // Restore form data on page load
        restoreFormData();

        // Expose clear function globally for manual clearing if needed
        window.clearHealthFormCache = clearFormCache;

        // Handle opening consent access modal from within health form modal (nested modal fix)
        const openConsentModalBtn = document.getElementById('openConsentAccessModal');
        if (openConsentModalBtn) {
            openConsentModalBtn.addEventListener('click', function() {
                // Get the consent access modal
                const consentModal = document.getElementById('requestConsentAccessModal');
                if (consentModal) {
                    // Show the nested modal
                    const modal = new bootstrap.Modal(consentModal, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modal.show();
                }
            });
        }

        // Initialize signature pad FIRST
        if (typeof window.initializeHealthForm === 'function') {
            console.log('Calling initializeHealthForm...');
            window.initializeHealthForm();
        } else {
            console.warn('initializeHealthForm function not found');
            // Alternative: Try to initialize directly if signature elements exist
            const canvas = document.getElementById("signature-pad");
            if (canvas && typeof SignaturePad !== "undefined") {
                console.log('Attempting direct signature pad initialization...');
                try {
                    const signaturePad = new SignaturePad(canvas, {
                        backgroundColor: "rgb(255,255,255)",
                        penColor: "rgb(0, 0, 0)",
                        minWidth: 1,
                        maxWidth: 3,
                        throttle: 16,
                        minDistance: 5,
                    });

                    // CRITICAL: Expose globally for AJAX access
                    window.signaturePad = signaturePad;
                    console.log('Signature pad initialized and exposed globally');

                    // Clear button functionality
                    const clearBtn = document.getElementById("clear-signature");
                    if (clearBtn) {
                        clearBtn.addEventListener('click', function() {
                            signaturePad.clear();
                            const hiddenInput = document.getElementById("signature-input");
                            if (hiddenInput) hiddenInput.value = '';
                        });
                    }
                } catch (error) {
                    console.error('Failed to initialize signature pad:', error);
                }
            }
        }

        // --- Faculty exemption: when Department == FACULTY disable Course and Grade level
        function ensureHiddenInput(container, id, name, value) {
            let existing = document.getElementById(id);
            if (existing) {
                existing.value = value;
                return existing;
            }
            const input = document.createElement('input');
            input.type = 'hidden';
            input.id = id;
            input.name = name;
            input.value = value;
            container.appendChild(input);
            return input;
        }

        function removeIfExists(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function toggleFacultyFields() {
            const dept = document.getElementById('department');
            if (!dept) return;
            const course = document.getElementById('course');
            const year = document.getElementById('year_level');

            // container to attach hidden inputs (use form element)
            const form = document.getElementById('healthInfoForm') || document.querySelector('form.health-form');
            if (!form) return;

            if (dept.value === 'FACULTY') {
                // Set visible UI to show N/A and disable inputs
                if (course) {
                    course.value = 'N/A';
                    course.setAttribute('disabled', 'disabled');
                    course.classList.add('bg-light');
                }
                if (year) {
                    // Insert an "N/A" option if not already present, then select it and disable the select
                    let naOption = year.querySelector('option[data-na-option]');
                    if (!naOption) {
                        naOption = document.createElement('option');
                        naOption.value = 'N/A';
                        naOption.textContent = 'N/A';
                        naOption.setAttribute('data-na-option', '1');
                        // it's good to keep it at the top so users see it
                        year.insertBefore(naOption, year.firstChild);
                    }
                    // store current selection so we can restore later
                    year.dataset.previousValue = year.value || '';
                    year.value = 'N/A';
                    year.setAttribute('disabled', 'disabled');
                    year.classList.add('bg-light');
                }

                // Ensure hidden inputs are present so disabled fields still submit
                ensureHiddenInput(form, 'hidden_course_input', 'course', 'N/A');
                ensureHiddenInput(form, 'hidden_year_input', 'year_level', 'N/A');
            } else {
                // Re-enable the original fields and remove hidden inputs
                if (course) {
                    course.removeAttribute('disabled');
                    course.classList.remove('bg-light');
                    // if the hidden input previously put N/A but there is an existing patient value, keep it
                }
                if (year) {
                    // restore previous selection if exists
                    const prev = year.dataset.previousValue || '';
                    if (prev && Array.from(year.options).some(o => o.value == prev)) {
                        year.value = prev;
                    } else {
                        // remove N/A if it was inserted and no previous value
                        const na = year.querySelector('option[data-na-option]');
                        if (na && (!year.value || year.value === 'N/A')) {
                            na.remove();
                        }
                    }
                    year.removeAttribute('disabled');
                    year.classList.remove('bg-light');
                }

                removeIfExists('hidden_course_input');
                removeIfExists('hidden_year_input');
            }
        }

        // wire change event and run on load
        const deptSelect = document.getElementById('department');
        if (deptSelect) {
            deptSelect.addEventListener('change', function() {
                // small timeout to allow any model-binding to complete
                setTimeout(toggleFacultyFields, 0);
            });
        }
        // run once on DOM load
        try {
            toggleFacultyFields();
        } catch (e) {
            console.error('toggleFacultyFields error', e);
        }


        // --- Enforce max 11 digits on number inputs (contact fields)
        function setupNumberFieldMaxDigits(id, maxDigits = 11) {
            const el = document.getElementById(id);
            if (!el) return;

            // Helper to trim to digits and limit length
            function sanitizeValue(value) {
                // keep only digits
                const digits = value.replace(/\D/g, '');
                return digits.slice(0, maxDigits);
            }

            // On input: sanitize value (covers typing, autofill)
            el.addEventListener('input', function(e) {
                const sanitized = sanitizeValue(this.value);
                if (this.value !== sanitized) {
                    // set caret to end by reassigning value
                    this.value = sanitized;
                }
            });

            // Prevent characters like 'e', '+', '-', '.' in number inputs (keycodes for older browsers)
            el.addEventListener('keydown', function(e) {
                // Allow: backspace(8), tab(9), enter(13), escape(27), delete(46), arrows(37-40), home(36), end(35)
                const allowed = [8, 9, 13, 27, 46, 35, 36, 37, 38, 39, 40];
                if (allowed.includes(e.keyCode)) return;

                // Block e, E, +, -, . keys
                if ([69, 101, 187, 189, 190].includes(e.keyCode)) {
                    e.preventDefault();
                    return;
                }

                // If length already at max and key is a number, prevent
                const isNumberKey = (e.key >= '0' && e.key <= '9');
                if (isNumberKey) {
                    const curr = (this.value || '').replace(/\D/g, '');
                    if (curr.length >= maxDigits) {
                        e.preventDefault();
                    }
                }
            });

            // Handle paste: sanitize pasted content
            el.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text') || '';
                const sanitized = sanitizeValue(paste);
                // Merge with existing digits up to max
                const existing = (this.value || '').replace(/\D/g, '');
                const spaceLeft = maxDigits - existing.length;
                const toInsert = sanitized.slice(0, spaceLeft);
                this.value = (existing + toInsert).slice(0, maxDigits);
            });
        }

        ['contact_no', 'father_contact_no', 'mother_contact_no', 'guardian_contact_no'].forEach(id => setupNumberFieldMaxDigits(id, 11));

        // Modal-based date picker - Horizontal Scroll
        const birthdateDisplay = document.getElementById('birthdate_display');
        const birthdateHidden = document.getElementById('birthdate');

        window.selectedDateValues = {};

        // Set initial display value if exists
        if (birthdateHidden && birthdateHidden.value) {
            birthdateDisplay.value = formatDisplayDate(birthdateHidden.value);
        }

        // Open modal on click
        if (birthdateDisplay) {
            birthdateDisplay.addEventListener('click', function() {
                openDatePickerModal();
            });
        }

        function formatDisplayDate(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return `${months[parseInt(parts[1]) - 1]} ${parseInt(parts[2])}, ${parts[0]}`;
            }
            return dateStr;
        }

        function openDatePickerModal() {
            const modal = new bootstrap.Modal(document.getElementById('datePickerModal'));

            // Pre-select existing date if available
            if (birthdateHidden.value) {
                const parts = birthdateHidden.value.split('-');
                if (parts.length === 3) {
                    window.selectedDateValues = {
                        year: parts[0],
                        month: parts[1],
                        day: parts[2]
                    };

                    // Highlight pre-selected values
                    setTimeout(() => {
                        document.querySelectorAll('.year-scroll .date-btn').forEach(btn => {
                            if (btn.textContent === parts[0]) btn.classList.add('selected');
                        });
                        document.querySelectorAll('.month-scroll .date-btn').forEach(btn => {
                            if (btn.onclick.toString().includes(`'${parts[1]}'`)) btn.classList.add('selected');
                        });
                        generateDays();
                    }, 100);
                }
            } else {
                window.selectedDateValues = {};
                document.getElementById('displayDate').textContent = 'No date selected';
                document.getElementById('confirmDateBtn').disabled = true;
            }

            modal.show();
        }

        window.selectYearHorizontal = function(year, btn) {
            // Clear previous selection
            document.querySelectorAll('.year-scroll .date-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');

            window.selectedDateValues.year = year;

            // Clear month and day when year changes
            delete window.selectedDateValues.month;
            delete window.selectedDateValues.monthName;
            delete window.selectedDateValues.day;

            // Clear month selection visually
            document.querySelectorAll('.month-scroll .date-btn').forEach(b => b.classList.remove('selected'));

            // Clear day container
            const container = document.getElementById('dayScrollContainer');
            container.innerHTML = '<div class="text-muted text-center p-2" style="font-size: 0.85rem;">Select year & month</div>';

            updateDisplayDate();
        };

        window.selectMonthHorizontal = function(month, monthName, btn) {
            // Clear previous selection
            document.querySelectorAll('.month-scroll .date-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');

            window.selectedDateValues.month = month;
            window.selectedDateValues.monthName = monthName;

            // Clear day when month changes
            delete window.selectedDateValues.day;

            updateDisplayDate();

            // Generate days
            if (window.selectedDateValues.year) {
                generateDays();
            }
        };

        window.selectDayHorizontal = function(day, btn) {
            // Clear previous selection
            document.querySelectorAll('.day-scroll .date-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');

            window.selectedDateValues.day = day;
            updateDisplayDate();
        };

        function generateDays() {
            const year = parseInt(window.selectedDateValues.year);
            const month = parseInt(window.selectedDateValues.month);

            if (!year || !month) return;

            const daysInMonth = new Date(year, month, 0).getDate();
            const container = document.getElementById('dayScrollContainer');
            container.innerHTML = '';

            for (let day = 1; day <= daysInMonth; day++) {
                const dayStr = String(day).padStart(2, '0');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'date-btn';
                btn.textContent = day;
                btn.onclick = function() {
                    selectDayHorizontal(dayStr, this);
                };

                // Pre-select if matches existing day
                if (window.selectedDateValues.day === dayStr) {
                    btn.classList.add('selected');
                }

                container.appendChild(btn);
            }
        }

        function updateDisplayDate() {
            const {
                year,
                monthName,
                day
            } = window.selectedDateValues;
            const displayEl = document.getElementById('displayDate');
            const confirmBtn = document.getElementById('confirmDateBtn');

            if (year && monthName && day) {
                displayEl.textContent = `${monthName} ${parseInt(day)}, ${year}`;
                confirmBtn.disabled = false;
            } else if (year && monthName) {
                displayEl.textContent = `${monthName} ${year} - Select day`;
                confirmBtn.disabled = true;
            } else if (year) {
                displayEl.textContent = `${year} - Select month & day`;
                confirmBtn.disabled = true;
            } else {
                displayEl.textContent = 'No date selected';
                confirmBtn.disabled = true;
            }
        }

        window.confirmDate = function() {
            const {
                year,
                month,
                day
            } = window.selectedDateValues;

            if (year && month && day) {
                const dateStr = `${year}-${month}-${day}`;
                birthdateHidden.value = dateStr;
                birthdateDisplay.value = formatDisplayDate(dateStr);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('datePickerModal'));
                if (modal) modal.hide();
            }
        };
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix: Always re-initialize consent exclusivity when modal is shown
        var healthModal = document.getElementById('healthInfoModal');
        if (healthModal && typeof setupExclusiveNone === 'function') {
            healthModal.addEventListener('shown.bs.modal', function() {
                setupExclusiveNone('consent', 'consent_none');
            });
        }
    });
</script>
@endpush