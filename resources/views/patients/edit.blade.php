@extends('layouts.app')

@section('content')
@push('styles')
@vite(['resources/css/patients/edit.css',
])
@endpush
<div class="main-content py-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="flex-grow-1 text-center">
            <h3 class="m-0">
                Edit <span class="text-primary">{{ data_get($patient, 'last_name') }}</span> Information
            </h3>
        </div>
        <a href="{{ route('patients.index') }}" class="btn-general btn-gray ms-3">Back</a>
    </div>



    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Personal Information</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input style="text-transform: capitalize;" required type="text" id="last_name" name="last_name" value="{{ $fieldValues['last_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="first_name" class="form-label">First Name</label>
                        <input style="text-transform: capitalize;" required type="text" id="first_name" name="first_name" value="{{ $fieldValues['first_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label style="text-transform: capitalize;" for="middle_name" class="form-label">Middle Name</label>
                        <input style="text-transform: capitalize;" type="text" id="middle_name" name="middle_name" value="{{ $fieldValues['middle_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-1">
                        <label for="suffix" class="form-label">Suffix</label>
                        <input style="text-transform: capitalize;" type="text" id="suffix" name="suffix" placeholder="Jr., Sr." value="{{ $fieldValues['suffix'] }}" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label for="sex" class="form-label">Sex</label>
                        <select id="sex" name="sex" class="form-select" required>
                            <option value="" disabled {{ $fieldValues['sex'] ? '' : 'selected' }}>Select Sex</option>
                            @foreach ($sexOptions as $sex)
                            <option value="{{ $sex }}" {{ $fieldValues['sex'] === $sex ? 'selected' : '' }}>
                                {{ $sex }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-2">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" id="age" name="age" value="{{ $fieldValues['age'] }}" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label for="department" class="form-label">Department</label>
                        <select id="department" name="department" class="form-select" required>
                            <option value="" disabled {{ $fieldValues['department'] ? '' : 'selected' }}>Select Department</option>
                            @foreach ($departmentOptions as $dept)
                            <option value="{{ $dept }}" {{ $fieldValues['department'] === $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="course" class="form-label">Course/Section</label>
                        <input required type="text" id="course" name="course" value="{{ $fieldValues['course'] }}" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <label for="year_level" class="form-label">Year Level</label>
                        <select id="year_level" name="year_level" class="form-select" required>
                            <option value="" disabled {{ $fieldValues['year_level'] ? '' : 'selected' }}>Select Year</option>
                            @foreach (['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9','Grade 10','Grade 11','Grade 12', '1st Year', '2nd Year', '3rd Year', '4th Year'] as $year)
                            <option value="{{ $year }}" {{ $fieldValues['year_level'] === $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="birthdate" class="form-label">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" value="{{ $fieldValues['birthdate'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="religion" class="form-label">Religion</label>
                        <input style="text-transform: capitalize;" type="text" id="religion" name="religion" value="{{ $fieldValues['religion'] }}" class="form-control" />
                    </div>

                    <div class="col-md-4">
                        <label for="contact_no" class="form-label">Contact No.</label>
                        <input type="number" id="contact_no" name="contact_no" value="{{ $fieldValues['contact_no'] }}" class="form-control" />
                    </div>

                    <div class="col-md-4">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input style="text-transform: capitalize;" type="text" id="nationality" name="nationality" value="{{ $fieldValues['nationality'] }}" class="form-control" />
                    </div>
                    <div class="col-md-8">
                        <label for="address" class="form-label">Address</label>
                        <input style="text-transform: capitalize;" type="text" id="address" name="address" value="{{ $fieldValues['address'] }}" class="form-control" />
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Parents & Guardians</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="father_name" class="form-label">Father's Name</label>
                        <input style="text-transform: capitalize;" type="text" id="father_name" name="father_name" value="{{ $fieldValues['father_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="father_contact_no" class="form-label">Father Contact No.</label>
                        <input type="number" id="father_contact_no" name="father_contact_no" value="{{ $fieldValues['father_contact_no'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="mother_name" class="form-label">Mother's Name</label>
                        <input style="text-transform: capitalize;" type="text" id="mother_name" name="mother_name" value="{{ $fieldValues['mother_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="mother_contact_no" class="form-label">Mother Contact No.</label>
                        <input type="number" id="mother_contact_no" name="mother_contact_no" value="{{ $fieldValues['mother_contact_no'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="guardian_name" class="form-label">Guardian Name</label>
                        <input style="text-transform: capitalize;" type="text" id="guardian_name" name="guardian_name" value="{{ $fieldValues['guardian_name'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="guardian_relationship" class="form-label">Guardian Relationship</label>
                        <input style="text-transform: capitalize;" type="text" id="guardian_relationship" name="guardian_relationship" value="{{ $fieldValues['guardian_relationship'] }}" class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <label for="guardian_contact_no" class="form-label">Guardian Contact No.</label>
                        <input type="number" id="guardian_contact_no" name="guardian_contact_no" value="{{ $fieldValues['guardian_contact_no'] }}" class="form-control" />
                    </div>
                    <div class="col-md-8">
                        <label for="guardian_address" class="form-label">Guardian Address</label>
                        <input style="text-transform: capitalize;" type="text" id="guardian_address" name="guardian_address" value="{{ $fieldValues['guardian_address'] }}" class="form-control" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== MEDICAL INFO ==================== --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Medical Information</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Allergies</label>
                        <div class="checkbox-container d-flex flex-wrap gap-2">
                            @foreach($allergyOptions as $opt)
                            <label class="checkbox-label d-flex align-items-center gap-1">
                                <input type="checkbox" name="allergies[]" value="{{ $opt['value'] }}"
                                    {{ in_array($opt['value'], $selFields['allergies'] ?? []) ? 'checked' : '' }}>
                                <span>{{ $opt['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="other_allergies" class="form-label">Other Allergies</label>
                        <input type="text" id="other_allergies" name="other_allergies" value="{{ $fieldValues['other_allergies'] }}" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Treatments</label>
                        <div class="checkbox-container d-flex flex-wrap gap-2">
                            @foreach($treatmentOptions as $opt)
                            <label class="checkbox-label d-flex align-items-center gap-1">
                                <input type="checkbox" name="treatments[]" value="{{ $opt['value'] }}"
                                    {{ in_array($opt['value'], $selFields['treatments'] ?? []) ? 'checked' : '' }}>
                                <span>{{ $opt['label'] }}</span>
                            </label>
                            @endforeach

                        </div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">COVID-19 Vaccination Status</label>
                        <div class="checkbox-container d-flex flex-wrap gap-2">
                            @foreach($covidOptions as $opt)
                            <label class="checkbox-label d-flex align-items-center gap-1">
                                <input type="checkbox" name="covid[]" value="{{ $opt['value'] }}"
                                    {{ in_array($opt['value'], $selFields['covid'] ?? []) ? 'checked' : '' }}>
                                <span>{{ $opt['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="flu_vaccine" class="form-label">Flu Vaccine</label>
                        <input type="text" id="flu_vaccine" name="flu_vaccine" value="{{ $fieldValues['flu_vaccine'] }}" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label for="other_vaccine" class="form-label">Other Vaccine</label>
                        <input type="text" id="other_vaccine" name="other_vaccine" value="{{ $fieldValues['other_vaccine'] }}" class="form-control" />
                    </div>
                    <div class="col-md-12">
                        <label for="medical_history" class="form-label">Medical History</label>
                        <textarea id="medical_history" name="medical_history" class="form-control" rows="3" placeholder="Kindly state any past and present medical conditions">{{ $fieldValues['medical_history'] }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="medication" class="form-label">Maintenance Medication</label>
                        <textarea id="medication" name="medication" class="form-control" rows="2">{{ $fieldValues['medication'] }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="lasthospitalization" class="form-label">Date and Reason of Last Hospitalization</label>
                        <input type="text" id="lasthospitalization" name="lasthospitalization" value="{{ $fieldValues['lasthospitalization'] }}" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="confirm-edit">
                <button type="submit" class="btn-general btn-blue">Save changes</button>
            </div>
        </div>

        {{-- ==================== CONSENT ==================== 
<div class="card mb-4 shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">Consent</h4>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Consent Options</label>
                <div class="consent-checkbox d-flex flex-column gap-2">
                    @foreach($consentOptions as $opt)
                    <label class="consent-label d-flex align-items-start gap-2">
                        <input type="checkbox" name="consent[]" value="{{ $opt }}" {{ in_array($opt, $selConsent) ? 'checked' : '' }}>
        <span>{{ $opt }}</span>
        </label>
        @endforeach
</div>
</div>
<div class="col-md-4">
    <label for="consent_by" class="form-label">Consent By</label>
    <input type="text" id="consent_by" name="consent_by" value="{{ old('consent_by', data_get($p, 'consent_by')) }}" class="form-control" />
</div>
</div>
</div>
</div>
--}}


</form>
</div>

@endsection

@push('scripts')
@vite(['resources/css/patients/edit.css',
    'resources/js/shared/checkbox.js',
])
<script>
    // Auto-resize textareas
    document.querySelectorAll("textarea").forEach(function(textarea) {
        textarea.style.height = "42px";
        textarea.style.height = textarea.scrollHeight + "px";

        textarea.addEventListener("input", function() {
            this.style.height = "42px"; // reset
            this.style.height = this.scrollHeight + "px";
        });
    });

    // Prevent Enter from submitting the form
    document.querySelectorAll('form.needs-validation').forEach(function(form) {
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
    });
</script>
@endpush