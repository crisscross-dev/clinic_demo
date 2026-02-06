<style>
    .pending-details {
        z-index: 1300 !important;
    }

    /* Scoped to the bootstrap modal body to avoid bleeding into other pages */
    #patientModalBody .pending-details h2 {
        margin: 0 0 8px;
        font-size: 22px;
    }

    #patientModalBody .modal-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin: 18px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #ccc;
    }

    #patientModalBody .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 20px;
    }

    #patientModalBody .detail-item strong {
        display: block;
        color: #222;
        font-size: 13px;
    }

    #patientModalBody .detail-item span {
        display: block;
        font-size: 13px;
        color: #555;
        overflow-wrap: anywhere;
    }
</style>

<div class="pending-details">
    <h2>{{trim( $patientData['last_name'] )}}, {{trim( $patientData['first_name'] )}} — Details</h2>

    <div class="modal-section-title">Personal Info</div>
    <div class="detail-grid">
        <div class="detail-item"><strong>Name</strong><span>{{trim( $patientData['last_name'] )}}, {{trim( $patientData['first_name'] )}}</span></div>
        <div class="detail-item"><strong>Middlename:</strong><span> {{ $patientData['middle_name'] }}</span></div>
        <div class="detail-item"><strong>Age:</strong><span>{{ $patientData['age'] }}</span></div>
        <div class="detail-item"><strong>Department:</strong><span>{{ $patientData['department'] }}</span></div>
        <div class="detail-item"><strong>Course/Section:</strong><span>{{ $patientData['course'] }}</span></div>
        <div class="detail-item"><strong>Year Level:</strong><span>{{ $patientData['year_level'] }}</span></div>
        <div class="detail-item"><strong>Sex:</strong><span>{{ $patientData['sex'] }}</span></div>
        <div class="detail-item"><strong>Nationality:</strong><span>{{ $patientData['nationality'] }}</span></div>
        <div class="detail-item"><strong>Birthdate</strong><span>{{ $formatted['birthdateStr'] }}</span></div>
        <div class="detail-item"><strong>Religion:</strong><span> {{ $patientData['religion'] }}</span></div>
        <div class="detail-item"><strong>Contact:</strong><span> {{ $patientData['contact_no'] }}</span></div>
        <div class="detail-item"><strong>Address:</strong><span>{{ $patientData['address'] }}</span></div>
    </div>

    <div class="modal-section-title">Family</div>
    <div class="detail-grid">
        <div class="detail-item"><strong>Father:</strong> <span>{{ $patientData['father_name'] }}</span></div>
        <div class="detail-item"><strong>Father Contact:</strong> <span>{{ $patientData['father_contact_no'] }}</span></div>
        <div class="detail-item"><strong>Mother:</strong> <span></span>{{ $patientData['mother_name'] }}</div>
        <div class="detail-item"><strong>Mother Contact:</strong><span>{{ $patientData['mother_contact_no'] }}</span></div>
        <div class="detail-item"><strong>Guardian:</strong> <span>{{ $patientData['guardian_name'] }}</span></div>
        <div class="detail-item"><strong>Guardian Relation:</strong><span>{{ $patientData['guardian_relationship'] }}</span></div>
        <div class="detail-item"><strong>Guardian Contact:</strong><span>{{ $patientData['guardian_contact_no'] }}</span></div>
        <div class="detail-item"><strong>Guardian Address:</strong><span> {{ $patientData['guardian_address'] }}</span></div>
    </div>

    <div class="modal-section-title">Medical</div>
    <div class="detail-grid">
        <div class="detail-item"><strong>Allergies:</strong><span>{!! $formatted['allergies'] !!}</span></div>
        <div class="detail-item"><strong>Other Allergies:</strong> <span>{{ $patientData['other_allergies'] }}</span></div>
        <div class="detail-item"><strong>Treatment:</strong><span>{!! $formatted['treatments'] !!}</span></div>
        <div class="detail-item"><strong>COVID Status:</strong><span>{!! $formatted['covid'] !!}</span></div>
        <div class="detail-item"><strong>Flu Vaccine:</strong><span>{{ $patientData['flu_vaccine'] }}</span></div>
        <div class="detail-item"><strong>Other Vaccine:</strong><span>{{ $patientData['other_vaccine'] }}</span></div>
        <div class="detail-item"><strong>Medical History:</strong><span>{{ $patientData['medical_history'] }}</span></div>
        <div class="detail-item"><strong>Maintenance Medication:</strong><span>{{ $patientData['medication'] }}</span></div>
        <div class="detail-item"><strong>Last Hospitalization:</strong><span>{{ $patientData['lasthospitalization'] }}</span></div>
    </div>

    <div class="modal-section-title">Consent</div>
    <div class="detail-grid">
        <div class="detail-item"><strong>Consent:</strong><span>{!! $formatted['consent'] !!}</span></div>
        <div class="detail-item"><strong>Consent by:</strong><span>{{ $patientData['consent_by'] }}</span></div>
        <div class="detail-item"><strong>Date Submitted:</strong><span>{{ $formatted['createdStr'] }}</span></div>
    </div>
    <div class="detail-item">
        <strong>Signature</strong>
        <span>
            @if($patient->signature)
            @php
            $sigPath = storage_path('app/private/' . ltrim($patient->signature, '/'));
            @endphp
            @if(file_exists($sigPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($sigPath)) }}"
                alt="Signature"
                style="max-width: 350px; max-height: 150px; border:1px solid #ccc; border-radius:6px;">
            @else
            <span class="text-muted">Signature file not found</span>
            @endif
            @else
            <span class="text-muted">—</span>
            @endif
        </span>
    </div>

    <div class="action-buttons pending-btn d-flex gap-2 mt-3">
        <form action="{{ route('pendings.approve', ['id' => data_get($patient,'id')]) }}" data-approve-type="patient" method="POST" class="approve-form">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-general btn-green">Approve</button>
        </form>

        <form action="{{ route('pendings.destroy', data_get($patient,'id')) }}" data-delete-type="patient" method="POST" class="delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-general btn-red">Reject</button>
        </form>
    </div>

</div>